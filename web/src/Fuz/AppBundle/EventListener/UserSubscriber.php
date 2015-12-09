<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\User;

class UserSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
                'preRemove',
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        if ($object instanceof User) {
            $query = $om->createQuery("
                DELETE FROM Fuz\AppBundle\Entity\Fiddle f
                WHERE f.user = :user
                AND f.visibility = :private
            ");

            $params = array(
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
