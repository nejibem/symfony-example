<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    public function userInfoAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle\Entity\User');

        $user = $userRepo->find($id);

        return $this->render('AppBundle:User:user_info.html.twig', [
            'user' => $user,
        ]);
    }

}
