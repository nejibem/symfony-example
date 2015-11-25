<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{

    public function findOneByRole($role)
    {
        $dql = 'SELECT g '
              .'FROM AppBundle:Group g '
              .'WHERE g.role = :role';

        return $this->getEntityManager()
                    ->createQuery($dql)
                    ->setParameter('role', $role)
                    ->getSingleResult();
    }

}