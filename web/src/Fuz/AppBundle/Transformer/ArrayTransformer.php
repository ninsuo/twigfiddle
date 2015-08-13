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

class ArrayTransformer implements DataTransformerInterface
{
    protected $separator;

    public function __construct($separator = ',')
    {
        $this->separator = $separator;
    }

    public function transform($tags)
    {
        return implode($this->separator, $tags);
    }

    public function reverseTransform($tags)
    {
        return array_unique(array_map(function ($tag) {
               return strtolower(trim($tag, " \n\r\t"));
           }, explode($this->separator, $tags)));
    }
}
