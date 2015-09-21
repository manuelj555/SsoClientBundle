<?php

namespace Ku\SsoClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        if ($request->query->has('q')) {
            $this->get('security.token_storage')->setToken(null);
        } else {
            $token = $this->get('security.token_storage')->getToken();

            dump($token);
        }


        return $this->render('base.html.twig', array());
    }

    public function otpValidateAction(Request $request)
    {

    }
}
