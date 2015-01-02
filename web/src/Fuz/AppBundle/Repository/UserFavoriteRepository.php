<?php

namespace Fuz\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;

/**
 * UserFavoriteRepository
 */
class UserFavoriteRepository extends EntityRepository
{

    public function getFavorite(Fiddle $fiddle, User $user = null)
    {
        if ((is_null($user)) || (is_null($fiddle->getId())))
        {
            return false;
        }

        $query = $this->_em->createQuery("
            SELECT uf
            FROM Fuz\AppBundle\Entity\UserFavorite uf
            WHERE uf.fiddle = :fiddle
            AND uf.user = :user
        ");

        $params = array (
                'fiddle' => $fiddle,
                'user' => $user,
        );

        $userFavorite = $query
           ->setParameters($params)
           ->getOneOrNullResult()
        ;

        return $userFavorite;
    }

}