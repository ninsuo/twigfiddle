<?php

namespace Fuz\AppBundle\Helper;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Fuz\AppBundle\Service\Utilities;
use Fuz\AppBundle\Entity\Fiddle;

class FiddleHelper
{

    protected $logger;
    protected $utilities;
    protected $em;
    protected $webConfig;

    public function __construct(LoggerInterface $logger, Utilities $utilities, EntityManager $em, array $webConfig)
    {
        $this->logger = $logger;
        $this->utilities = $utilities;
        $this->em = $em;
        $this->webConfig = $webConfig;
    }

    public function save($hash, $revision, Fiddle $fiddle)
    {
        if (is_null($hash))
        {
            $this->em->transactional(function($em) use ($fiddle)
            {
                $this->registerEmptyHash($em, $fiddle);
            });
        }






    }

    public function registerEmptyHash(EntityManager $em, Fiddle $fiddle)
    {
        $repository = $em->getRepository('FuzAppBundle:Fiddle');
        do
        {
            $hash = $this->utilities->randomString($this->webConfig['default_fiddle_hash_size']);
        }
        while ($repository->hashExists($hash));
        $fiddle->setHash($hash);
        $fiddle->setRevision(1);
        $this->em->persist($fiddle);
        $this->em->flush();
    }

}
