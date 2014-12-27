<?php

namespace Fuz\AppBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Fuz\AppBundle\Helper\DoctrineHelper;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;

class SaveFiddle
{

    protected $logger;
    protected $utilities;
    protected $doctrineHelper;
    protected $em;
    protected $webConfig;

    public function __construct(LoggerInterface $logger, Utilities $utilities, DoctrineHelper $doctrineHelper,
       EntityManager $em, array $webConfig)
    {
        $this->logger = $logger;
        $this->utilities = $utilities;
        $this->doctrineHelper = $doctrineHelper;
        $this->em = $em;
        $this->webConfig = $webConfig;
    }

    public function save($hash, $revision, Fiddle $fiddle, User $user = null)
    {
        if (is_null($hash))
        {
            $fiddle = $this->doctrineHelper->lock($fiddle, DoctrineHelper::LOCK_READ,
               function () use ($fiddle, $user)
            {
                return $this->createRandomHash($fiddle, $user);
            });
        }

        else if ($revision > 0)
        {
            $this->em->merge($fiddle);
            $this->em->flush();
        }

        else
        {
            $fiddle = $this->doctrineHelper->lock($fiddle, DoctrineHelper::LOCK_READ,
               function () use ($fiddle, $user)
            {
                return $this->createNewRevision($fiddle, $user);
            });
        }

        return $fiddle;
    }

    public function createRandomHash(Fiddle $fiddle, User $user = null)
    {
        $repository = $this->em->getRepository('FuzAppBundle:Fiddle');

        $this->logger->debug("Need to create a new hash.");

        do
        {
            $hash = $this->utilities->randomString($this->webConfig['default_fiddle_hash_size']);
            $this->logger->debug("Testing hash: {$hash}");
        }
        while ($repository->hashExists($hash));

        $this->logger->debug("Hash is empty: {$hash}.");

        $fiddle->setHash($hash);
        $fiddle->setRevision(1);

        if ($user)
        {
            $fiddle->setUser($user);
        }

        $this->em->persist($fiddle);
        $this->em->flush();

        return $fiddle;
    }

    public function createNewRevision(Fiddle $fiddle, User $user = null)
    {
        $repository = $this->em->getRepository('FuzAppBundle:Fiddle');
        $revision = $repository->getNextRevisionNumber($fiddle->getHash());

        $clone = clone $fiddle;
        $clone->setRevision($revision);

        if ($user)
        {
            $clone->setUser($user);
        }

        $this->em->persist($clone);
        $this->em->flush();

        return $clone;
    }

}
