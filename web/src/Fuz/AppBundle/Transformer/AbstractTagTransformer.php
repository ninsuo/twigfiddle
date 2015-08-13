<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Fuz\AppBundle\Api\TagInterface;
use Fuz\AppBundle\Api\TagContainerInterface;

abstract class AbstractTagTransformer implements DataTransformerInterface
{
    public function transform($tagCollection)
    {
        $tags = array();

        foreach ($tagCollection as $tag) {
            if ($tag instanceof TagInterface) {
                $tags[] = $tag->getTag();
            }
        }

        return implode(',', $tags);
    }

    public function reverseTransformHelper(TagContainerInterface $object, TagInterface $empty, $tags = null)
    {
        $oldTags = array();
        foreach ($object->getTags() as $tagObj) {
            $oldTags[] = $tagObj->getTag();
        }
        $newTags = array_unique(array_map(array($this, 'normalizeTag'), explode(',', $tags)));

        $toDel = array_diff($oldTags, $newTags);
        $toAdd = array_diff($newTags, $oldTags);

        foreach ($object->getTags() as $tagObj) {
            if (in_array($tagObj->getTag(), $toDel)) {
                $object->getTags()->removeElement($tagObj);
            }
        }

        foreach ($toAdd as $tag) {
            if (strlen($tag) == 0) {
                continue;
            }
            $tagObj = clone $empty;
            $tagObj->setTag($tag);
            $object->getTags()->add($tagObj);
        }

        return $object->getTags();
    }

    protected function normalizeTag($tag)
    {
        return strtolower(trim($tag, " \n\r\t"));
    }

    abstract public function reverseTransform($tags);
}
