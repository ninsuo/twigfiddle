<?php
/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\Agent\FiddleAgent;

class TwigExtensionsManager extends BaseService
{

    protected $twigExtensionsConfiguration;

    public function __construct(array $twigExtensionsConfiguration)
    {
        $this->twigExtensionsConfiguration = $twigExtensionsConfiguration;
    }

    public function loadTwigExtensions(FiddleAgent $agent, \Twig_Environment $environment)
    {
        $this->checkHasTwigEnvironmentLoaded();

        $extension = $agent->getFiddle()->getTwigExtension();
        if (!$extension) {
            $this->logger->debug("No twig extensions requested.");
            return;
        }

        $autoloader = $this->twigExtensionsConfiguration['directory']
           .'/Twig-extensions-'
           .str_replace(DIRECTORY_SEPARATOR, '', $extension)
           .'/lib/Twig/Extensions/Autoloader.php';

        if (!is_file($autoloader)) {
            $agent->addError(Error::E_UNKNOWN_TWIG_EXTENSION, array(
                'extension'  => $extension,
                'autoloader' => $autoloader,
            ));
            throw new StopExecutionException();
        }

        require($autoloader);
        \Twig_Extensions_Autoloader::register();
        $this->registerAllExtensions($environment);

        return $this;
    }

    protected function checkHasTwigEnvironmentLoaded()
    {
        if (!class_exists("\Twig_Environment")) {
            throw new \LogicException('A twig environment should be loaded first.');
        }

        return $this;
    }

    protected function registerAllExtensions(\Twig_Environment $environment)
    {
        $extensions = array('Text', 'I18n', 'Intl', 'Array', 'Date');
        foreach ($extensions as $extension) {
            $class = "\Twig_Extensions_Extension_{$extension}";
            if (class_exists($class)) {
                $environment->addExtension(new $class);
            }
        }
    }

}
