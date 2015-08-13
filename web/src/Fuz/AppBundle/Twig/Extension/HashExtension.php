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

class HashExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
              new \Twig_SimpleFilter('sha1', 'sha1'),
              new \Twig_SimpleFilter('md5', 'md5'),
        );
    }

    public function getName()
    {
        return 'FuzAppBundle:Hash';
    }
}
