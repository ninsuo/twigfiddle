<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\TwigEngine;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Agent\FiddleAgent;

abstract class AbstractTwigEngine extends BaseService implements TwigEngineInterface
{
    /**
     * @var FiddleAgent
     */
    protected $agent;

    public function __construct(FiddleAgent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * The first coomment of all compiled twig file contains the twig file name since the very first Twig's version.
     * This method just extracts it.
     *
     * @param string $cacheDirectory
     * @param array $files
     * @return array
     */
    public function extractTemplateName($content)
    {
        $templateName = null;
        $tokens = token_get_all($content);
        foreach ($tokens as $token)
        {
            if (!is_array($token))
            {
                continue;
            }
            list($identifier, $string) = $token;
            if ($identifier !== T_COMMENT)
            {
                continue;
            }
            $templateName = trim(str_replace(array ('/*', '*/'), '', $string));
            break;
        }
        return $templateName;
    }

    /**
     * Loads the Twig C extension (until now, the extension is always located
     * at the same place).
     *
     * @param string $sourceDirectory
     * @throws \RuntimeException
     */
    protected function loadCExtension($sourceDirectory)
    {
        /*
         * Can't overwrite extension_dir, need to add this as command-line argument
         *
        $oldExtensionDir = ini_get("extension_dir");
        $newExtensionDir = "{$sourceDirectory}/ext/twig/.libs/";
        $library = "{$newExtensionDir}/twig.so";

        if (!file_exists($library) || !is_readable($library))
        {
            throw new \RuntimeException("The Twig engine you requested does not have a C extension.");
        }
         */

        ini_set('extension_dir', $newExtensionDir);
        if (!dl('twig.so'))
        {
            ini_set('extension_dir', $oldExtensionDir);
            throw new \RuntimeException("Can't load the C extension {$library}. Is enable_dl set to On in php.ini?");
        }
        ini_set('extension_dir', $oldExtensionDir);
    }

}
