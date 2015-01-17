<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\StringLoader;

use Symfony\Component\Yaml\Yaml;
use Fuz\Framework\StringLoader\StringLoaderInterface;

class YamlStringLoader implements StringLoaderInterface
{

    public function load($stream)
    {
        return Yaml::parse($stream);
    }

}
