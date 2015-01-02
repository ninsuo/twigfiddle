<?php

namespace Fuz\AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Fuz\AppBundle\Entity\UserFavorite;

class UserFavoriteSubscriber implements EventSubscriber
{

    protected $tags;

    public function getSubscribedEvents()
    {
        return array (
                'prePersist',
                'postPersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof UserFavorite)
        {
            $this->tags = $object->getTags();
            $object->setTags(new ArrayCollection());
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        if ($object instanceof UserFavorite)
        {
            foreach ($this->tags as $tag)
            {
                $tag->setUserFavorite($object);
                $om->persist($tag);
            }
            $object->setTags($this->tags);

            $om->flush();
        }
    }

}
