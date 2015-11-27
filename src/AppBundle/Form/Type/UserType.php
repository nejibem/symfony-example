<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email');
        $builder->add('username', 'text');

        $builder
            ->add('password', 'repeated', array(
                'first_name' => 'Password',
                'second_name' => 'Confirm',
                'type' => 'password',
            ))
            ->add('groups', 'entity', array(
                'class' => 'AppBundle:Group',
                'property'     => 'role',
                'multiple'     => true,
                'expanded'     => true
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}