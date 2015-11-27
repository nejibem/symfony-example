<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;

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
        $this->setName('app:user:generate')
            ->setDescription('Generate root User')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Who do you want to the root user to be?'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'What do you want the root user password to be?'
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'What do you want the root email password to be?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $validator = $this->getContainer()->get('validator');

        $username = $input->getArgument('username') ? $input->getArgument('username') : 'root';
        $password = $input->getArgument('password') ? $input->getArgument('password') : 'password';
        $email = $input->getArgument('email') ? $input->getArgument('email') : 'root@domain.com';

        try
        {
            $user = $this->createEntity($username, $password, $email);
            if( $validator->validate($user) )
            {
                $em->persist($user);
                $em->flush();
            }
            $success = true;
        }
        catch(\Exception $e)
        {
            $success = false;
        }

        $result = " Result: <fg=yellow;options=bold>". ( $success ? "Success" : "Failure" ) ."</>";

        $output->writeln("");
        $output->writeln(" Attempting to create user");
        $output->writeln($result);
        $output->writeln("");
    }

    private function createEntity($username, $password, $email)
    {
        $securityEncodeFactory = $this->getContainer()->get('security.encoder_factory');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $groupRepo = $em->getRepository('AppBundle\Entity\Group');
        $roleAdminGroup = $groupRepo->findOneByRole('ROLE_ADMIN');

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->addGroup($roleAdminGroup);
        $user->setIsActive(true);

        $encoder = $securityEncodeFactory->getEncoder($user);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);

        return $user;
    }

}