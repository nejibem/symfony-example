<?php

namespace AppBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use AppBundle\Entity\UserLogin;

class AuthenticationHandler extends ContainerAware implements AuthenticationSuccessHandlerInterface
{
    function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $em = $this->container->get('doctrine')->getManager();

        $userLogin = new UserLogin();
        $userLogin->setIpAddress( $request->getClientIp() );
        $userLogin->setUser( $token->getUser() );

        $em->persist($userLogin);
        $em->flush();

        return new RedirectResponse($this->container->get('router')->generate('default_security_target'));
    }
}