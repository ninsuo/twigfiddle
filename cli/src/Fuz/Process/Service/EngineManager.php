<?php

namespace Fuz\Process\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\TwigEngine\TwigEngineInterface;
use Fuz\Process\Agent\FiddleAgent;

class EngineManager extends BaseService
{

    protected $container;
    protected $fiddleConfiguration;
    protected $twigSourceConfiguration;

    public function __construct(ContainerInterface $container, array $fiddleConfiguration,
       array $twigSourceConfiguration)
    {
        $this->container = $container;
        $this->fiddleConfiguration = $fiddleConfiguration;
        $this->twigSourceConfiguration = $twigSourceConfiguration;
    }

    public function loadTwigEngine(FiddleAgent $agent)
    {
        $fiddle = $agent->getFiddle();
        if (is_null($fiddle))
        {
            throw new \LogicException("You should load a fiddle before trying to prepare its twig engine.");
        }

        $version = $fiddle->getTwigVersion();
        $this->logger->debug("Loading Twig Engine version: {$version}\n");
        $engine = $this->findRightEngine($version);
        if (is_null($engine))
        {
            $agent->addError(Error::E_ENGINE_NOT_FOUND, array ('version' => $version));
            throw new StopExecutionException();
        }

        $this->logger->debug(sprintf("Twig Engine %s loaded successfully.", get_class($engine)));
        $agent->setEngine($engine);

        $sourceDirectory = $this->getTwigSourceDirectory($version);
        $this->logger->debug(sprintf("Twig source for version %s is loacated at: %s.", $version, $sourceDirectory));
        $agent->setSourceDirectory($sourceDirectory);

        return $this;
    }

    public function findRightEngine($expectedVersion)
    {
        $service = null;
        $engineServiceIds = $this->container->findTaggedServiceIds('twig.engine');
        foreach ($engineServiceIds as $serviceId => $tags)
        {
            foreach ($tags as $tag)
            {
                if (!array_key_exists('versions', $tag))
                {
                    continue;
                }
                if (in_array(strtolower($expectedVersion),
                      array_map('trim', array_map('strtolower', explode("/", $tag['versions'])))))
                {
                    $service = $this->container->get($serviceId);
                    break;
                }
            }
            if (!is_null($service))
            {
                break;
            }
        }
        if (($service) && (!($service instanceof TwigEngineInterface)))
        {
            throw new \LogicException("The Twig Engine version {$expectedVersion} has been found, but does not implement the TwigEngineInterface interface.");
        }
        return $service;
    }

    public function getTwigSourceDirectory($version)
    {
        if (strpos($version, DIRECTORY_SEPARATOR))
        {
            throw new \LogicException("Looks like the version number contains a directory separator: {$version}.");
        }

        $dir = $this->twigSourceConfiguration['directory'] . DIRECTORY_SEPARATOR . $version;
        if (!is_dir($dir))
        {
            throw new IOException("Twig source's directory does not exist.");
        }

        return $dir;
    }

    public function getEngineFromAgent(FiddleAgent $agent)
    {
        $engine = $agent->getEngine();
        if (is_null($engine))
        {
            throw new \LogicException("Twig Engine has not been loaded in this fiddle.");
        }
        return $engine;
    }

}
