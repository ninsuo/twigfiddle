<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Twig\Extension;

class DebugExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
              new \Twig_SimpleFunction('dump', ['Symfony\Component\VarDumper\VarDumper', 'dump']),
        ];
    }

    public function getName()
    {
        return 'FuzAppBundle:Debug';
    }
}
