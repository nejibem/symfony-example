<?php

namespace AppBundle\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

class OAuthUserProvider extends BaseOAuthUserProvider
{
    protected $em;
    protected $encoderFactory;

    /**
     * EhubOAuthProvider constructor.
     * @param EntityManager $em
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EntityManager $em, EncoderFactoryInterface $encoderFactory)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param string $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $qb = $this->em->createQueryBuilder();
        $user = $qb->select('u')
                    ->from('AppBundle:User', 'u')
                    ->where('u.username = :username')
                    ->setParameter('username', $username)
                    ->getQuery()
                    ->getOneOrNullResult();

        return $user;
    }

    /**
     * @param UserResponseInterface $response
     * @return User|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $socialId = $response->getUsername();
        $service = $response->getResourceOwner()->getName();
        $username = sprintf('%s-%s', $service, $socialId);
        $email = $response->getEmail();

        // hack for twitter not returning email
        if( null === $email || $email === "" )
        {
            $service = $response->getResourceOwner()->getName();
            $email = "$username@$service.test";
        }

        switch ($service) {
            case 'google':
                $property = 'googleId';
                break;
            case 'facebook':
                $property = 'facebookId';
                break;
            case 'twitter':
                $property = 'twitterId';
                break;
        }

        $qb = $this->em->createQueryBuilder();
        $user = $qb->select("u")
                    ->from("AppBundle:User", "u")
                    ->where("u.email = :email")
                    ->orWhere("u.$property = :socialId")
                    ->setParameter("email", $email)
                    ->setParameter("socialId", $socialId)
                    ->getQuery()
                    ->getOneOrNullResult();

        if( $user === null )
        {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->{'set'.$property}($socialId);

            // generate random password
            $factory = $this->encoderFactory->getEncoder($user);
            $password = $factory->encodePassword(md5(uniqid(mt_rand(), true)), $user->getSalt());
            $user->setPassword($password);

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'AppBundle\\Entity\\User';
    }
}