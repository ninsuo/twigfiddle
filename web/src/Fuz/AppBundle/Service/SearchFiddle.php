<?php

namespace Fuz\AppBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;

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


}