<?php

namespace Fuz\AppBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Fuz\AppBundle\Helper\DoctrineHelper;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;

class SaveFiddle
{

    /* This pattern should be the same in FuzAppBundle:Fiddle's routing */
    const HASH_PATTERN = '/^[a-zA-Z0-9-]{1,16}$/';

    protected $logger;
    protected $utilities;
    protected $doctrineHelper;
    protected $em;
    protected $session;
    protected $router;
    protected $webConfig;

    public function __construct(LoggerInterface $logger, Utilities $utilities, DoctrineHelper $doctrineHelper,
       EntityManager $em, Session $session, Router $router, array $webConfig)
    {
        $this->logger = $logger;
        $this->utilities = $utilities;
        $this->doctrineHelper = $doctrineHelper;
        $this->em = $em;
        $this->session = $session;
        $this->router = $router;
        $this->webConfig = $webConfig;
    }

    /**
     * Checks whether user can use the save button, to save
     * current fiddle's instance or the fav form.
     *
     * @param Fiddle $fiddle
     * @param User $user
     * @return bool
     */
    public function canClickSave(Fiddle $fiddle, User $user = null)
    {
        if (is_null($fiddle->getId()))
        {
            return true;
        }

        if ($this->ownsFiddle($fiddle, $user))
        {
            return true;
        }

        return false;
    }

    /**
     * Checks whether user owns current fiddle's revision.
     *
     * @param Fiddle $fiddle
     * @param User $user
     * @return bool
     */
    public function ownsFiddle(Fiddle $fiddle, User $user = null)
    {
        if ($fiddle->getUser() && $user && $fiddle->getUser()->isEqualTo($user))
        {
            return true;
        }

        if ($this->session->has('recent-fiddles') &&
           in_array($fiddle->getId(), $this->session->get('recent-fiddles')))
        {
            return true;
        }

        return false;
    }

    /**
     * Checks whether a hash can be used to reference a fiddle
     *
     * @param string|null $hash
     * @return bool
     */
    public function validateHash($hash)
    {
        if (is_null($hash))
        {
            return false;
        }

        if (!preg_match(self::HASH_PATTERN, $hash))
        {
            return false;
        }

        $routes = $this->router->getRouteCollection();
        $reserved = array ();
        foreach ($routes->getIterator() as $route)
        {
            $path = substr($route->getPath(), 1);
            if (false !== strpos($path, '/'))
            {
                $path = substr($path, 0, strpos($path, '/'));
            }
            if (!in_array($path, $reserved))
            {
                $reserved[] = $path;
            }
        }
        if (in_array($hash, $reserved))
        {
            return false;
        }

        return true;
    }

    public function save($hash, $revision, Fiddle $fiddle, User $user = null)
    {
        if (is_null($hash))
        {
            $fiddle = $this->doctrineHelper->lock($fiddle, DoctrineHelper::LOCK_READ,
               function () use ($fiddle)
            {
                return $this->createRandomHash($fiddle);
            });
        }
        else if ($revision > 0)
        {
            $this->em->persist($fiddle);
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

    protected function createRandomHash(Fiddle $fiddle)
    {
        $repository = $this->em->getRepository('FuzAppBundle:Fiddle');

        $this->logger->debug("Need to create a new hash.");

        do
        {
            $hash = $this->utilities->randomString($this->webConfig['default_fiddle_hash_size'], 'abcdefghijklmnopqrstuvwxyz012345689');
            $this->logger->debug("Testing hash: {$hash}");
        }
        while ($repository->hashExists($hash));

        $this->logger->debug("Hash is empty: {$hash}.");

        $fiddle->setHash($hash);
        $fiddle->setRevision(1);

        $this->em->persist($fiddle);
        $this->em->flush();

        return $fiddle;
    }

    protected function createNewRevision(Fiddle $fiddle, User $user = null)
    {
        $repository = $this->em->getRepository('FuzAppBundle:Fiddle');
        $revision = $repository->getNextRevisionNumber($fiddle->getHash());

        $clone = clone $fiddle;
        $clone->setHash(strtolower($clone->getHash()));
        $clone->setRevision($revision);

        if ($user)
        {
            $clone->setUser($user);
        }

        $this->em->persist($clone);
        $this->em->flush();

        return $clone;
    }

    /**
     * Used to let unregistered users update recent fiddle's revisions they own.
     *
     * @param int $id
     */
    public function saveFiddleToSession($id, User $user = null)
    {
        if (!is_null($user))
        {
            return;
        }
        if (!$this->session->has('recent-fiddles'))
        {
            $this->session->set('recent-fiddles', array ($id));
        }
        else
        {
            $list = array_merge($this->session->get('recent-fiddles'), array ($id));
            $this->session->set('recent-fiddles', array_slice($list, 0, $this->webConfig['max_fiddles_in_session']));
        }
    }

}
