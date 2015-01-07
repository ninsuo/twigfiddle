<?php

namespace Fuz\AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Fuz\AppBundle\Entity\User;
use Fuz\AppBundle\Entity\Fiddle;

class UserSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return array (
                'preRemove',
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        if ($object instanceof User)
        {
            $query = $om->createQuery("
                DELETE FROM Fuz\AppBundle\Entity\Fiddle f
                WHERE f.user = :user
                AND f.visibility = :private
            ");

            $params = array (
                    'user' => $object->getId(),
                    'private' => Fiddle::VISIBILITY_PRIVATE,
            );

            $query
               ->setParameters($params)
               ->execute()
            ;
        }
    }

}
