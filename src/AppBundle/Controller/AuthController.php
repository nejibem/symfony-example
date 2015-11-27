<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AuthController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if( $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') )
        {
            return $this->redirect($this->generateUrl('default_security_target'));
        }

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        }
        else
        {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return $this->render('AppBundle:Auth:login.html.twig', [
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error'         => $error,
        ]);
    }

}
