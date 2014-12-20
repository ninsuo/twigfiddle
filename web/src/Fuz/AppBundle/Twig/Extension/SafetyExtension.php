<?php

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
