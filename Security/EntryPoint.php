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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class EntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var HttpUtils
     */
    private $httpUtils;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    private $loginSsoUrl;
    private $loginFormUrl;

    /**
     * EntryPoint constructor.
     *
     * @param HttpUtils             $httpUtils
     * @param UrlGeneratorInterface $urlGenerator
     * @param                       $loginSsoUrl
     * @param                       $loginFormUrl
     */
    public function __construct(HttpUtils $httpUtils, UrlGeneratorInterface $urlGenerator, $loginSsoUrl, $loginFormUrl)
    {
        $this->httpUtils = $httpUtils;
        $this->urlGenerator = $urlGenerator;
        $this->loginSsoUrl = $loginSsoUrl;
        $this->loginFormUrl = $loginFormUrl;
    }

    /**
     * Starts the authentication scheme.
     *
     * @param Request                 $request       The request that resulted in an AuthenticationException
     * @param AuthenticationException $authException The exception that started the authentication process
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if($this->httpUtils->checkRequestPath($request, 'ku_sso_client_otp_validate')){
            // Si estamos en la página de chequeo, no vamos a mandar a login nuevamente
            throw new ServiceUnavailableHttpException(null, 'No se pudo conectar');
        }

        if (!$authException instanceof AuthenticationServiceException) {
            $validationPath = $this->urlGenerator->generate('ku_sso_client_otp_validate', array(), true);
            $targetPath = $request->getUri();

            return new RedirectResponse(
                $this->loginSsoUrl . '?_target_path=' . ($validationPath . '?_target_path=' . rawurlencode($targetPath))
            );
        }
    }
}