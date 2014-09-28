<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{

    public function getUserByProviderId($provider, $providerId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
           ->from('FuzAppBundle:User', 'u')
           ->where('u.provider = :provider')
           ->andWhere('u.providerId = :providerId')
           ->setParameter('provider', $provider)
           ->setParameter('providerId', $providerId)
           ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();
        if (count($result))
        {
            return $result[0];
        }
        return null;
    }

}
