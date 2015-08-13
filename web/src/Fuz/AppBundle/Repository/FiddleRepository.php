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
 * FiddleRepository.
 */
class FiddleRepository extends EntityRepository
{
    public function getEmptyFiddle(User $user = null, $hash = null)
    {
        $fiddle = new Fiddle();
        $fiddle->setHash($hash);
        $fiddle->setRevision(0);
        if ($user) {
            $fiddle->setUser($user);
        }

        return $fiddle;
    }

    public function getFiddle($hash, $revision, User $user = null)
    {
        if (is_null($hash)) {
            return $this->getEmptyFiddle($user);
        }

        $query = $this->_em->createQuery("
            SELECT f
            FROM Fuz\AppBundle\Entity\Fiddle f
            WHERE f.hash = :hash
            AND f.revision = :revision
            AND (
                f.visibility <> :private
                OR f.user = :user
            )
        ");

        $params = array(
                'hash' => $hash,
                'revision' => $revision <= 0 ?: $revision,
                'private' => Fiddle::VISIBILITY_PRIVATE,
                'user' => $user ? $user->getId() : -1,
        );

        $fiddle = $query
           ->setParameters($params)
           ->getOneOrNullResult()
        ;

        return $fiddle ?: $this->getEmptyFiddle($user, $hash);
    }

    public function incrementVisitCount(Fiddle $fiddle)
    {
        if ($fiddle->getId()) {
            $fiddle->setVisitsCount($fiddle->getVisitsCount() + 1);
            $this->_em->flush($fiddle);
        }
    }

    public function hashExists($hash)
    {
        $query = $this->_em->createQuery("
            SELECT COUNT(f.id)
            FROM Fuz\AppBundle\Entity\Fiddle f
            WHERE f.hash = :hash
        ");

        $params = array(
                'hash' => $hash,
        );

        $count = $query
           ->setParameters($params)
           ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    public function getNextRevisionNumber($hash)
    {
        $query = $this->_em->createQuery("
            SELECT MAX(f.revision)
            FROM Fuz\AppBundle\Entity\Fiddle f
            WHERE f.hash = :hash
        ");

        $params = array(
                'hash' => $hash,
        );

        $max = $query
           ->setParameters($params)
           ->getSingleScalarResult()
        ;

        return $max + 1;
    }

    public function setOwner(User $user, array $fiddleIds)
    {
        $query = $this->_em->createQuery("
            UPDATE Fuz\AppBundle\Entity\Fiddle f
            SET f.user = :user
            WHERE f.id IN (:fiddle_ids)
        ");

        $params = array(
                'user' => $user->getId(),
                'fiddle_ids' => $fiddleIds,
        );

        $query
           ->setParameters($params)
           ->execute()
        ;
    }

    public function getRevisionList(Fiddle $fiddle, User $user = null)
    {
        if (is_null($fiddle->getId())) {
            return array();
        }

        $query = $this->_em->createQuery("
            SELECT f.revision, f.creationTm
            FROM Fuz\AppBundle\Entity\Fiddle f
            WHERE f.hash = :hash
            AND (
                f.visibility <> :private
                OR f.user = :user
            )
            ORDER BY f.revision ASC
        ");

        $params = array(
                'hash' => $fiddle->getHash(),
                'private' => Fiddle::VISIBILITY_PRIVATE,
                'user' => $user ? $user->getId() : -1,
        );

        $revisions = $query
           ->setParameters($params)
           ->getArrayResult()
        ;

        return $revisions;
    }
}
