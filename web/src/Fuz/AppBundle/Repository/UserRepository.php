<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository.
 */
class UserRepository extends EntityRepository
{
    public function getUserByResourceOwnerId($resourceOwner, $resourceOwnerId)
    {
        $query = $this->_em->createQuery("
            SELECT u
            FROM Fuz\AppBundle\Entity\User u
            WHERE u.resourceOwner = :resourceOwner
            AND u.resourceOwnerId = :resourceOwnerId
        ");

        $params = [
                'resourceOwner'   => $resourceOwner,
                'resourceOwnerId' => $resourceOwnerId,
        ];

        $user = $query
           ->setMaxResults(1)
           ->setParameters($params)
           ->getOneOrNullResult()
        ;

        return $user;
    }
}
