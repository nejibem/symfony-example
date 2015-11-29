<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default_security_target")
     */
    public function indexAction()
    {
//        $mail = $this->get('app.mail');
//        $mail->send('nejibem@gmail.com', 'nejibem@gmail.com', 'subject', 'blaa blaa...');


        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * @Route("/user/{id}", name="user_info", requirements={
     *     "id": "\d+"
     * })
     */
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
