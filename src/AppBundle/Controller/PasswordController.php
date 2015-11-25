<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Type\PasswordType;
use AppBundle\Entity\User;

class PasswordController extends Controller
{

    public function emailAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = null;
        $success = false;
        $successMessages = array();
        $errorMessages = array();
        $email = $this->get('request')->request->get('email', null);

        if( $email )
        {
            $user = $em->getRepository('AppBundle:User')->findOneByEmailForReset($email);
            if( $user )
            {
                try
                {
                    $user->generatePasswordResetKey();
                    $em->persist($user);
                    $em->flush();
                    $this->sendPasswordResetEmail($user);
                    $successMessages[] = 'You have been emailed a link to reset your password';
                    $success = true;
                }
                catch( \Exception $e )
                {
                    $success = true;
                    $errorMessages[] = 'Error occurred sending password reset email.';
                }
            }
            else
            {
                $errorMessages[] = 'Could not find an Active User for that email address.';
            }
        }

        return $this->render('AppBundle:Password:email_form.html.twig', [
            'email' => $email,
            'user'  => $user,
            'success' => $success,
            'successMessages' => $successMessages,
            'errorMessages' => $errorMessages,
        ]);
    }

    private function sendPasswordResetEmail($user)
    {
        $swiftMailer = $this->get('mailer');
        $mailgunDomain = $this->container->getParameter('mailgun_domain');

        $messageBody = '<p>Dear '. $user->getUsername() .',</p>'
                      .'<p>To reset your password <a href="http://'. $mailgunDomain .'/password/reset/'. $user->getPasswordResetKey() .'">Click Here</a></p>';
        $subject = 'Password reset';
        $message = \Swift_Message::newInstance()
                        ->setSubject($subject)
                        ->setFrom('no-reply@'.$mailgunDomain)
                        ->setTo($user->getEmail())
                        ->setBody( $messageBody, 'text/html');

        return $swiftMailer->send($message);
    }

    public function resetResponseAction()
    {
        return $this->render('AppBundle:Password:password_response.html.twig', [
            'successMessages' => $this->get('session')->getFlashBag()->get('successMessages'),
            'errorMessages' => array(),
        ]);
    }

    public function resetAction($passwordResetKey)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByPasswordResetKey($passwordResetKey);
        if( $user )
        {
            return $this->form( $user );
        }
        else
        {
            return new Response('Unauthorized access.', 403);
        }
    }

    private function form(User $user)
    {
        $successMessages = array();
        $errorMessages = array();
        $passwordType = new PasswordType();
        $options = array();
        $form = $this->createForm($passwordType, $user, $options);

        $request = $this->getRequest();
        $form->handleRequest($request);

        if( $request->getMethod() == 'POST' && $form->isValid() )
        {
            if( $this->savePassword($form) )
            {
                $this->get('session')->getFlashBag()->add('successMessages', 'Your password has been changed!');
                return $this->redirect($this->generateUrl('user_password_reset_response'));
            }
            else
            {
                $errorMessages[] = "An error occurred Saving password.";
            }
        }
        elseif( $request->getMethod() == 'POST' && $form->isValid() == false )
        {
            $errorMessages[] = "An error occurred in the form, see below.";
        }

        return $this->render('AppBundle:Password:password_form.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'successMessages' => $successMessages,
            'errorMessages' => $errorMessages,
        ]);
    }

    private function savePassword($form)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $user =  $form->getData();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            // encrypt the password and remove the password reset key
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            if( $user->getPasswordResetKey() )
            {
                $user->setPasswordResetKey(null);
            }

            $em->persist($user);
            $em->flush();
            $success = true;
        }
        catch( \Exception $e )
        {
            $success = false;
        }

        return $success;
    }

}
