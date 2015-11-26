<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, g')
            ->leftJoin('u.groups', 'g')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find a User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }

    public function findOneByPasswordResetKey($passwordResetKey)
    {
        $dql = 'SELECT u '
              .'FROM AppBundle:User u '
              .'WHERE u.passwordResetKey = :passwordResetKey '
              .'AND u.isActive = 1 '
              .'AND ( u.passwordResetKey IS NOT NULL AND u.passwordResetKey != \'\' ) ';

        return $this->getEntityManager()
                    ->createQuery($dql)
                    ->setParameter('passwordResetKey', $passwordResetKey)
                    ->getOneOrNullResult();
    }

    public function findOneByEmailForReset($email)
    {
        $dql = 'SELECT u '
              .'FROM AppBundle:User u '
              .'WHERE u.email = :email '
              .'AND u.isActive = 1 ';

        return $this->getEntityManager()
                    ->createQuery($dql)
                    ->setParameter('email', $email)
                    ->getOneOrNullResult();
    }

}