<?php

namespace Fuz\AppBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\Request;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;
use Fuz\AppBundle\Entity\BrowseFilters;
use Fuz\AppBundle\Service\Paginator;

/**
 * Known bug, see http://stackoverflow.com/questions/27890452/filter-tags-with-doctrine
 */
class SearchFiddle
{

    protected $logger;
    protected $em;
    protected $paginator;
    protected $webConfig;

    public function __construct(LoggerInterface $logger, EntityManager $em, Paginator $paginator, array $webConfig)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->paginator = $paginator;
        $this->webConfig = $webConfig;
    }

    /**
     * Searches for fiddles
     *
     * @param BrowseFilters $criteria
     * @param User $user
     */
    public function search(Request $request, BrowseFilters $criteria, User $user = null)
    {
        $qbCount = $this->em->createQueryBuilder();
        $qbCount->select('COUNT(DISTINCT f.id)');

        $count = $this
           ->createSearchQueryBuilder($qbCount, $criteria, $user)
           ->getQuery()
           ->getSingleScalarResult()
        ;

        $qbResult = $this->em->createQueryBuilder();
        $qbResult->select('DISTINCT f.hash, f.revision');
        $this->createSearchQueryBuilder($qbResult, $criteria, $user);

        $pagination = $this->paginator->paginate($request, $qbResult, $count);
        $fiddles = $qbResult->getQuery()->getArrayResult();

        return array($pagination, $fiddles);
    }

    public function createSearchQueryBuilder(QueryBuilder $qb, BrowseFilters $criteria, User $user = null)
    {
        $qb
           ->from('Fuz\AppBundle\Entity\Fiddle', 'f')
           ->leftJoin('Fuz\AppBundle\Entity\User', 'u',  Expr\Join::WITH, $qb->expr()->eq('f.user', ':user'))
           ->leftJoin('f.tags', 't')
           ->leftJoin('Fuz\AppBundle\Entity\UserBookmark', 'b', Expr\Join::WITH, $qb->expr()->andX(
              $qb->expr()->eq('b.fiddle', 'f.id'),
              $qb->expr()->eq('b.user', ':user')
           ))
           ->leftJoin('Fuz\AppBundle\Entity\UserBookmarkTag', 'bt', Expr\Join::WITH, $qb->expr()->eq('b.id', 'bt.userBookmark'))
           ->where(
               $qb->expr()->andX(
                   $qb->expr()->orX(
                       $qb->expr()->eq('u.id', ':user'),
                       $qb->expr()->isNull('u.id')
                   ),
                   $qb->expr()->orX(
                       $qb->expr()->eq('f.visibility', ':public'),
                       $qb->expr()->eq('f.user', ':user')
                   ),
                  $this->applyKeywordsFilter($criteria, $qb, $user),
                  $this->applyTagsFilter($criteria, $qb, $user),
                  $this->applyBookmarkFilter($criteria, $qb, $user),
                  $this->applyMineFilter($criteria, $qb, $user),
                  $this->applyVisibilityFilter($criteria, $qb, $user)
               )
           )
           ->orderBy('f.creationTm', 'DESC')
        ;

        $qb
           ->setParameter('public', Fiddle::VISIBILITY_PUBLIC)
           ->setParameter('user', $user)
        ;

        return $qb;
    }

    public function applyKeywordsFilter(BrowseFilters $criteria, QueryBuilder $qb, User $user = null)
    {
        $andF = $qb->expr()->andX();
        $andB = $qb->expr()->andX();
        foreach ($criteria->getKeywords() as $key => $keyword)
        {
            if (strlen($keyword) > 0)
            {
                $andF->add($qb->expr()->like('f.title', ":keyword_{$key}"));
                $andB->add($qb->expr()->like('b.title', ":keyword_{$key}"));
                $qb->setParameter(":keyword_{$key}", '%' . addcslashes($keyword, '_%') . '%');
            }
        }
        return $qb->expr()->orX($andF, $andB);
    }

    public function applyTagsFilter(BrowseFilters $criteria, QueryBuilder $qb, User $user = null)
    {
        /**
         * @XXX I expected to use ->andX (cumulative tags), but there is no GROUP_CONCAT nor FIND_IN_SET
         * in Doctrine, this leaded to an ugly workaround... Should add it soon!.
         */
        $andF = $qb->expr()->orX();
        $andB = $qb->expr()->orX();
        foreach ($criteria->getTags() as $key => $keyword)
        {
            if (strlen($keyword) > 0)
            {
                $andF->add($qb->expr()->eq('t.tag', ":tag_{$key}"));
                $andB->add($qb->expr()->eq('bt.tag', ":tag_{$key}"));
                $qb->setParameter(":tag_{$key}", $keyword);
            }
        }
        return $qb->expr()->orX($andF, $andB);
    }

    public function applyBookmarkFilter(BrowseFilters $criteria, QueryBuilder $qb, User $user = null)
    {
        if (!is_null($user) && $criteria->getBookmark())
        {
            return $qb->expr()->isNotNull('b.id');
        }
    }

    public function applyMineFilter(BrowseFilters $criteria, QueryBuilder $qb, User $user = null)
    {
        if (!is_null($user) && $criteria->getMine())
        {
            return $qb->expr()->eq('f.user', ':user');
        }
    }

    public function applyVisibilityFilter(BrowseFilters $criteria, QueryBuilder $qb, User $user = null)
    {
        if (!is_null($user) && $criteria->getVisibility())
        {
            $qb->setParameter('visibility', $criteria->getVisibility());
            return $qb->expr()->eq('f.visibility', ':visibility');
        }
    }

}
