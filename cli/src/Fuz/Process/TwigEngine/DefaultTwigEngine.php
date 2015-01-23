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

class DefaultTwigEngine extends BaseService implements TwigEngineInterface
{

    /**
     * From all released versions of Twig, there are backward compatibility to load a template.
     *
     * The only change was the way the cache directory is declared: at the very beginning, cache
     * directory (in fact, compilation directory) were given in the twig loader. But now, the cache
     * directory is given on Environment's options.
     *
     * @param string $sourceDirectory
     * @param string $cacheDirectory
     * @param string $template
     * @param array $context
     * @return string
     */
    public function render($sourceDirectory, $cacheDirectory, $template, array $context = array ())
    {
        require($sourceDirectory . DIRECTORY_SEPARATOR . '/lib/Twig/Autoloader.php');
        \Twig_Autoloader::register();

        $executionDirectory = dirname($template);
        $mainTemplate = basename($template);

        $twigLoader = new \Twig_Loader_Filesystem($executionDirectory, $cacheDirectory);
        $twigEnvironment = new \Twig_Environment($twigLoader, array ('cache' => $cacheDirectory));

        $templateObject = $twigEnvironment->loadTemplate($mainTemplate);

        ob_start();
        $templateObject->display($context);
        $result = ob_get_clean();

        return $result;
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

}
