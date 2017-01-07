<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\Entity;

class Deprecation
{
    protected $message;
    protected $file;
    protected $line;

    public function __construct($message, $file, $line)
    {
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function isIgnored()
    {
        switch ($this->message) {

            // Used internally by V1TwigEngine class (useful in older versions)
            case 'The Twig_Autoloader class is deprecated since version 1.21 and will be removed in 2.0. Use Composer instead.':
            case 'Using Twig_Autoloader is deprecated since version 1.21. Use Composer instead.':

                return true;
        }

        return false;
    }
}
