<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{

    public function getUserByResourceOwnerId($resourceOwner, $resourceOwnerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
           ->from('FuzAppBundle:User', 'u')
           ->where('u.resourceOwner = :resourceOwner')
           ->andWhere('u.resourceOwnerId = :resourceOwnerId')
           ->setParameter('resourceOwner', $resourceOwner)
           ->setParameter('resourceOwnerId', $resourceOwnerId)
           ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();
        if (count($result))
        {
            return $result[0];
        }
        return null;
    }

}
