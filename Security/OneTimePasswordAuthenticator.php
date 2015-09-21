<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ku\SsoClientBundle\Security;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class OneTimePasswordAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationSuccessHandlerInterface
{

    /**
     * @var HttpUtils
     */
    private $httpUtils;

    /**
     * @var UriSigner
     */
    private $uriSigner;
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var UserDataDecrypter
     */
    private $decrypter;

    /**
     * OneTimePasswordAuthenticator constructor.
     *
     * @param HttpUtils         $httpUtils
     * @param UriSigner         $uriSigner
     * @param ClientInterface   $httpClient
     * @param UserDataDecrypter $decrypter
     */
    public function __construct(HttpUtils $httpUtils, UriSigner $uriSigner, ClientInterface $httpClient, UserDataDecrypter $decrypter)
    {
        $this->httpUtils = $httpUtils;
        $this->uriSigner = $uriSigner;
        $this->httpClient = $httpClient;
        $this->decrypter = $decrypter;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $otp = $token->getCredentials();

        try {
            $response = $this->httpClient->get(sprintf('?_otp=%s', rawurlencode($otp)));
        } catch (ServerException $ex) {
            dump($ex->getMessage(), $ex->getResponse());
            // hacer log de esto
            throw new AuthenticationServiceException($ex->getMessage(), 0, $ex);
        }

        $encryptedData = $response->getBody()->getContents();
        $data = unserialize($this->decrypter->decrypt($encryptedData));

        $authenticatedToken = new PreAuthenticatedToken($data['username'], null, $providerKey, array('ROLE_USER'));
        $authenticatedToken->setAttributes($data['attributes']);

        return $authenticatedToken;
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken and $token->getProviderKey() == $providerKey;
    }

    public function createToken(Request $request, $providerKey)
    {
        if (!$this->httpUtils->checkRequestPath($request, 'ku_sso_client_otp_validate')) {
            return;
        }

        if (!($otp = $request->get('_otp', null)) || !$request->get('_target_path')) {
            return;
        }

        if (!$this->uriSigner->check($request->getSchemeAndHttpHost() . $request->getRequestUri())) {
            throw new BadRequestHttpException('Invalid Uri');
        }

        return new PreAuthenticatedToken('sso.', $otp, $providerKey);
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new RedirectResponse($request->get('_target_path'));
    }
}