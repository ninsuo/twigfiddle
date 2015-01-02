<?php

namespace Fuz\AppBundle\Transformer;

use Fuz\AppBundle\Api\TagInterface;
use Fuz\AppBundle\Api\TagContainerInterface;

class AbstractTagTransformer
{

    public function transform($tagCollection)
    {
        $tags = array ();

        foreach ($tagCollection as $tag)
        {
            if ($tag instanceof TagInterface)
            {
                $tags[] = $tag->getTag();
            }
        }

        return implode(',', $tags);
    }

    public function reverseTransformHelper(TagContainerInterface $object, TagInterface $empty, $tags = null)
    {
        $oldTags = array ();
        foreach ($object->getTags() as $tagObj)
        {
            $oldTags[] = $tagObj->getTag();
        }
        $newTags = array_unique(array_map(array($this, 'normalizeTag'), explode(',', $tags)));

        $toDel = array_diff($oldTags, $newTags);
        $toAdd = array_diff($newTags, $oldTags);

        foreach ($object->getTags() as $tagObj)
        {
            if (in_array($tagObj->getTag(), $toDel))
            {
                $object->getTags()->removeElement($tagObj);
            }
        }

        foreach ($toAdd as $tag)
        {
            if (strlen($tag) == 0)
            {
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

}
