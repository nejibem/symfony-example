<?php

namespace AppBundle\Service\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;

use AppBundle\Entity\UserLogin;

class AuthenticationSuccessHandler extends ContainerAware implements AuthenticationSuccessHandlerInterface
{
    private $router;
    private $em;

    /**
     * Constructor
     * @param RouterInterface   $router
     * @param EntityManager     $em
     */
    public function __construct(RouterInterface $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $userLogin = new UserLogin();
        $userLogin->setIpAddress( $request->getClientIp() );
        $userLogin->setUser( $token->getUser() );

        $this->em->persist($userLogin);
        $this->em->flush();

        return new RedirectResponse($this->router->generate('default_security_target'));
    }
}