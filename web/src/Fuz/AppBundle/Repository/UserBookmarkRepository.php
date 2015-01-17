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
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;

/**
 * UserBookmarkRepository
 */
class UserBookmarkRepository extends EntityRepository
{

    public function getBookmark(Fiddle $fiddle, User $user = null)
    {
        if ((is_null($user)) || (is_null($fiddle->getId())))
        {
            return false;
        }

        $query = $this->_em->createQuery("
            SELECT ub
            FROM Fuz\AppBundle\Entity\UserBookmark ub
            WHERE ub.fiddle = :fiddle
            AND ub.user = :user
        ");

        $params = array (
                'fiddle' => $fiddle,
                'user' => $user,
        );

        $userBookmark = $query
           ->setParameters($params)
           ->getOneOrNullResult()
        ;

        return $userBookmark;
    }

}