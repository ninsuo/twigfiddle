<?php

namespace Fuz\AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Fuz\AppBundle\Entity\UserBookmark;

class UserBookmarkSubscriber implements EventSubscriber
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
        if ($object instanceof UserBookmark)
        {
            $this->tags = $object->getTags();
            $object->setTags(new ArrayCollection());
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        if ($object instanceof UserBookmark)
        {
            foreach ($this->tags as $tag)
            {
                $tag->setUserBookmark($object);
                $om->persist($tag);
            }
            $object->setTags($this->tags);

            $om->flush();
        }
    }

}
