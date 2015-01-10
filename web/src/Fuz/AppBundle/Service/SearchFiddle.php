<?php

namespace Fuz\AppBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;
use Fuz\AppBundle\Entity\BrowseFilters;

class SearchFiddle
{

    protected $logger;
    protected $em;
    protected $webConfig;

    public function __construct(LoggerInterface $logger, EntityManager $em, array $webConfig)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->webConfig = $webConfig;
    }

    /**
     * Searches for fiddles
     *
     * @param BrowseFilters $criteria
     * @param User $user
     */
    public function search(BrowseFilters $criteria, User $user = null)
    {
        $qb = $this->em->createQueryBuilder();

        $qb
           ->select('DISTINCT f.hash, f.revision')
           ->from('Fuz\AppBundle\Entity\Fiddle', 'f')
           ->leftJoin('f.user', 'u')
           ->leftJoin('f.tags', 't')
           ->leftJoin('Fuz\AppBundle\Entity\UserBookmark', 'b', Expr\Join::WITH, $qb->expr()->andX(
              $qb->expr()->eq('b.fiddle', 'f.id'),
              $qb->expr()->eq('b.user', ':user')
           ))
           ->leftJoin('Fuz\AppBundle\Entity\UserBookmarkTag', 'bt', Expr\Join::WITH, $qb->expr()->eq('b.id', 'bt.userBookmark'))
           ->where(
               $qb->expr()->andX(
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

        $query = $qb->getQuery();
        $query->setMaxResults(10);
        $array = $query->getArrayResult();

        return $array;
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
                $qb->setParameter(":keyword_{$key}", $keyword);
            }
        }
        return $qb->expr()->orX($andF, $andB);
    }

    public function applyTagsFilter(BrowseFilters $criteria, QueryBuilder $qb, User $user = null)
    {
        $andF = $qb->expr()->andX();
        $andB = $qb->expr()->andX();
        foreach ($criteria->getTags() as $key => $keyword)
        {
            if (strlen($keyword) > 0)
            {
                $andF->add($qb->expr()->like('t.tag', ":tag_{$key}"));
                $andB->add($qb->expr()->like('bt.tag', ":tag_{$key}"));
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
        if (!is_null($user) && $criteria->getMine())
        {
            $qb->setParameter('visibility', $criteria->getVisibility());
            return $qb->expr()->eq('f.visibility', ':visibility');
        }
    }

}
