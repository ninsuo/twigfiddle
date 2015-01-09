<?php

namespace Fuz\AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class SimpleTagsTransformer implements DataTransformerInterface
{

    public function transform($tags)
    {
        return implode(',', $tags);
    }

    public function reverseTransform($tags)
    {
        return array_unique(array_map(function ($tag)
           {
               return strtolower(trim($tag, " \n\r\t"));
           }, explode(',', $tags)));
    }

}
