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

class SafetyExtension extends \Twig_Extension
{

    public function __construct($kernelDir)
    {
        $this->path = realpath($kernelDir . '/../../') . '/';
    }

    public function getFilters()
    {
        return array (
                new \Twig_SimpleFilter('hide_project_path',
                   function($string)
                   {
                       return str_replace($this->path, 'twigfiddle:', $string);
                   }),
        );
    }

    public function getName()
    {
        return 'FuzAppBundle:Safety';
    }

}
