<?php

namespace Fuz\AppBundle\Helper;

use Doctrine\ORM\EntityManager;

class DoctrineHelper
{

    protected $em;

    const LOCK_READ = 'READ';
    const LOCK_WRITE = 'WRITE';

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function lock($entity, $lockType, $callable)
    {
        $tableName = $this->em->getClassMetadata(get_class($entity))->getTableName();
        $escapedTableName = '`' . str_replace('`', '``', $tableName) . '`';

        if (!in_array($lockType, array (self::LOCK_READ, self::LOCK_WRITE)))
        {
            throw new \LogicException("Unexpected lock type given: {$lockType}");
        }

        $this->em->getConnection()->exec("LOCK TABLES {$escapedTableName} {$lockType}");

        try
        {
            $return = $this->em->transactional(function($em) use ($callable)
            {
                return call_user_func($callable, $em);
            });

            $this->em->getConnection()->exec("UNLOCK TABLES");
            return $return;
        }
        catch (\Exception $e)
        {
            $this->em->getConnection()->exec("UNLOCK TABLES");
            throw $e;
        }
    }

}
