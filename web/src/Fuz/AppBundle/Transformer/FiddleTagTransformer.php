<?php

namespace Fuz\AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\FiddleTag;

class FiddleTagTransformer extends AbstractTagTransformer implements DataTransformerInterface
{

    protected $fiddle;

    public function __construct(Fiddle $fiddle = null)
    {
        $this->fiddle = $fiddle;
    }

    public function reverseTransform($tags)
    {
        $empty = new FiddleTag();
        $empty->setFiddle($this->fiddle);

        return parent::reverseTransformHelper($this->fiddle, $empty, $tags);
    }

}
