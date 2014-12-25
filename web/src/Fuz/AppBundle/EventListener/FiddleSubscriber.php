<?php

namespace Fuz\AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Fuz\AppBundle\Entity\Fiddle;

class FiddleSubscriber implements EventSubscriber
{

    protected $context;
    protected $templates;

    public function getSubscribedEvents()
    {
        return array (
                'prePersist',
                'postPersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Fiddle)
        {
            $this->context = $entity->getContext();
            $entity->setContext(null);
            $this->templates = $entity->getTemplates();
            $entity->setTemplates(new ArrayCollection());
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof Fiddle)
        {
            $this->context->setFiddle($entity);
            $em->persist($this->context);

            foreach ($this->templates as $template)
            {
                $template->setFiddle($entity);
                $em->persist($template);
            }

            $em->flush();
        }
    }

}
