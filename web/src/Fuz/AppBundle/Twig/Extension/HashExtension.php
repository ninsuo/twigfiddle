<?php

namespace Fuz\AppBundle\Twig\Extension;

class HashExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array (
              new \Twig_SimpleFilter('sha1', 'sha1'),
              new \Twig_SimpleFilter('md5', 'md5'),
        );
    }

    public function getName()
    {
        return 'FuzAppBundle:Hash';
    }

}
