<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;


class GenerateCommand extends ContainerAwareCommand
{

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
   }

    protected function configure()
    {
        $this->setName('nejibem:user:generate')
            ->setDescription('Generate root User')
            ->addArgument(
                'username',
                InputArgument::OPTIONAL,
                'Who do you want to the root user to be?'
            )
            ->addArgument(
                'password',
                InputArgument::OPTIONAL,
                'What do you want the root user password to be?'
            )
            ->addArgument(
                'email',
                InputArgument::OPTIONAL,
                'What do you want the root email password to be?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $securityEncodeFactory = $this->getContainer()->get('security.encoder_factory');

        $text = 'Attempting to create default user';

        $username = $input->getArgument('username') ? $input->getArgument('username') : 'root';
        $password = $input->getArgument('password') ? $input->getArgument('password') : 'password';
        $email = $input->getArgument('email') ? $input->getArgument('email') : 'root@domain.com';

        $success = null;
        try
        {
            $groupUser = new Group();
            $groupUser->setName('ROLE_USER');
            $groupUser->setRole('ROLE_USER');

            $groupAdmin = new Group();
            $groupAdmin->setName('ROLE_ADMIN');
            $groupAdmin->setRole('ROLE_ADMIN');

            $user = new User();
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setEmail($email);
            $user->addGroup( $groupAdmin );
            $user->setIsActive(true);

            $encoder = $securityEncodeFactory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            $em->persist($groupUser);
            $em->persist($groupAdmin);
            $em->persist($user);
            $em->flush();
            $success = true;
        }
        catch ( \Exception $e )
        {
            $success = false;
        }

        if( $success )
        {
            $text .= " - Success!";
        }
        else
        {
            $text .= " - Failure!";
        }

        $output->writeln($text);
    }

}