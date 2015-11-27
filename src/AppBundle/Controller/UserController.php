<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\Type\UserType;
use AppBundle\Entity\User;

class UserController extends Controller
{

    /**
     * @Route("/admin/user", name="admin_user_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:User:user_index.html.twig', [
            'users' => $users,
            'successMessages' =>  $this->get('session')->getFlashBag()->get('successMessages'),
            'errorMessages' => array(),
        ]);
    }

    /**
     * @Route("/admin/user/switch-active/{id}", name="admin_user_switch_active", requirements={
     *     "id": "\d+"
     * })
     */
    public function switchActiveAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneById($id);

        $switchActive = $user->getIsActive() ? false : true;
        $user->setIsActive( $switchActive );
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_user_index'));
    }

    /**
     * @Route("/admin/user/edit/{id}", name="admin_user_edit", requirements={
     *     "id": "\d+"
     * })
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneById($id);

        return $this->form($request, $user);
    }

    /**
     * @Route("/admin/user/add", name="admin_user_add")
     */
    public function addAction(Request $request)
    {
        return $this->form($request, new User());
    }

    private function form(Request $request, User $user)
    {
        $successMessages = array();
        $errorMessages = array();
        $userType = new UserType();
        $options = array();
        $form = $this->createForm($userType, $user, $options);

        $form->handleRequest($request);

        if( $request->getMethod() == 'POST' && $form->isValid() )
        {
            $this->save($form);
            $this->get('session')->getFlashBag()->add('successMessages', 'Your changes were saved!');

            return $this->redirect($this->generateUrl('admin_user_index'));
        }

        return $this->render('AppBundle:User:user_form.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'successMessages' => $successMessages,
            'errorMessages' => $errorMessages,
        ]);
    }

    private function save($form)
    {
        $em = $this->getDoctrine()->getManager();
        $user =  $form->getData();

        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);

        if( $user->getId() === null && $user->getGroups()->count() == 0 )
        {
            $group = $em->getRepository('AppBundle:Group')->findOneByRole('ROLE_USER');
            $user->addGroup($group);
        }

        $em->persist($user);
        $em->flush();
    }

}
