<?php

namespace Fuz\AppBundle\Twig\Extension;

class DebugExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return array (
              new \Twig_SimpleFunction('dump', array('Symfony\Component\VarDumper\VarDumper', 'dump')),
        );
    }

    public function getName()
    {
        return 'FuzAppBundle:Debug';
    }

}
