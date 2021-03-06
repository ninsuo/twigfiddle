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
use Fuz\Process\Agent\FiddleAgent;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\TwigEngine\TwigEngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class EngineManager extends BaseService
{
    protected $container;
    protected $fiddleConfiguration;
    protected $twigSourceConfiguration;

    public function __construct(ContainerInterface $container, array $fiddleConfiguration,
       array $twigSourceConfiguration)
    {
        $this->container               = $container;
        $this->fiddleConfiguration     = $fiddleConfiguration;
        $this->twigSourceConfiguration = $twigSourceConfiguration;
    }

    public function loadTwigEngine(FiddleAgent $agent)
    {
        $fiddle = $agent->getFiddle();
        if (is_null($fiddle)) {
            throw new \LogicException('You should load a fiddle before trying to prepare its twig engine.');
        }

        $engine  = $fiddle->getTwigEngine();
        $version = $fiddle->getTwigVersion();

        $this->logger->debug("Loading Twig Engine: {$engine}\n");
        $engineService = $this->findRightEngine($engine, $version);
        if (is_null($engineService)) {
            $agent->addError(Error::E_ENGINE_NOT_FOUND, ['engine' => $engine]);
            throw new StopExecutionException();
        }

        $this->logger->debug(sprintf('Twig Engine %s loaded successfully.', get_class($engineService)));
        $agent->setEngine($engineService);

        $sourceDirectory = $this->getTwigSourceDirectory($version);
        $this->logger->debug(sprintf('Twig engine for version %s is loacated at: %s.', $version, $sourceDirectory));
        $agent->setSourceDirectory($sourceDirectory);

        return $this;
    }

    public function findRightEngine($engine, $version)
    {
        $service          = null;
        $engineServiceIds = $this->container->findTaggedServiceIds('twig.loader');

        // Remove "Twig-" on "Twig-1.16.3"
        $version = substr($version, 5);

        foreach ($engineServiceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!array_key_exists('support', $tag)) {
                    continue;
                }
                foreach (explode(',', $tag['support']) as $support) {
                    list($min, $max) = array_map('trim', explode('->', trim($support)));
                    if (version_compare($version, $min) >= 0 && version_compare($version, $max) <= 0) {
                        $service = $this->container->get($serviceId);
                        break;
                    }
                }
                if (!is_null($service)) {
                    break;
                }
            }
            if (!is_null($service)) {
                break;
            }
        }
        if (($service) && (!($service instanceof TwigEngineInterface))) {
            throw new \LogicException("The Twig Engine {$engine} has been found, but does not implement the TwigEngineInterface interface.");
        }

        return $service;
    }

    public function getTwigSourceDirectory($version)
    {
        if (strpos($version, DIRECTORY_SEPARATOR)) {
            throw new \LogicException("Looks like the version number contains a directory separator: {$version}.");
        }

        $dir = $this->twigSourceConfiguration['directory'].DIRECTORY_SEPARATOR.$version;
        if (!is_dir($dir)) {
            throw new IOException("Twig source's directory does not exist.");
        }

        return $dir;
    }

    public function getEngineFromAgent(FiddleAgent $agent)
    {
        $engine = $agent->getEngine();
        if (is_null($engine)) {
            throw new \LogicException('Twig Engine has not been loaded in this fiddle.');
        }

        return $engine;
    }
}
