<?php

namespace Fuz\AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\FiddleTag;

class FiddleTagTransformer implements DataTransformerInterface
{

    protected $fiddle;

    public function __construct(Fiddle $fiddle = null)
    {
        $this->fiddle = $fiddle;
    }

    public function transform($tagCollection)
    {
        $tags = array ();

        foreach ($tagCollection as $fiddleTag)
        {
            $tags[] = $fiddleTag->getTag();
        }

        return implode(',', $tags);
    }

    public function reverseTransform($tags)
    {
        if (is_null($this->fiddle))
        {
            throw new TransformationFailedException("Can't reverseTransform fiddle's tags without Doctrine-attached entity.");
        }

        $oldTags = array ();
        foreach ($this->fiddle->getTags() as $fiddleTag)
        {
            $oldTags[] = $fiddleTag->getTag();
        }
        $newTags = explode(',', $tags);

        $toDel = array_diff($oldTags, $newTags);
        $toAdd = array_diff($newTags, $oldTags);

        foreach ($this->fiddle->getTags() as $fiddleTag)
        {
            if (in_array($fiddleTag->getTag(), $toDel))
            {
                $this->fiddle->getTags()->removeElement($fiddleTag);
            }
        }

        foreach ($toAdd as $tag)
        {
            $tag = trim($tag, " \n\r\t");
            if (strlen($tag) == 0)
            {
                continue ;
            }
            $fiddleTag = new FiddleTag();
            $fiddleTag->setFiddle($this->fiddle);
            $fiddleTag->setTag($tag);
            $this->fiddle->getTags()->add($fiddleTag);
        }

        return $this->fiddle->getTags();
    }

}
