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

class FiddleSubscriber implements EventSubscriber
{
    protected $context;
    protected $templates;
    protected $tags;

    public function getSubscribedEvents()
    {
        return [
                'prePersist',
                'postPersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof Fiddle) {
            if (is_null($object->getUser())) {
                $object->setVisibility(Fiddle::VISIBILITY_PUBLIC);
            }

            $this->context = $object->getContext();
            $object->setContext(null);
            $this->templates = $object->getTemplates();
            $object->clearTemplates();
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $om     = $args->getObjectManager();
        $object = $args->getObject();
        if ($object instanceof Fiddle) {
            $this->context->setFiddle($object);
            $om->persist($this->context);

            foreach ($this->templates as $template) {
                $object->addTemplate($template);
                $om->persist($template);
            }

            $om->flush();
        }
    }
}
