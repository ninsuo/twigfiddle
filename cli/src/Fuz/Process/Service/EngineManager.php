<?php

namespace Fuz\Process\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\TwigEngine\TwigEngineInterface;
use Fuz\Process\Agent\FiddleAgent;

class EngineManager extends BaseService
{

    protected $container;
    protected $fiddleConfiguration;
    protected $twigEnginesConfiguration;

    public function __construct(ContainerInterface $container, array $fiddleConfiguration,
       array $twigEnginesConfiguration)
    {
        $this->container = $container;
        $this->fiddleConfiguration = $fiddleConfiguration;
        $this->twigEnginesConfiguration = $twigEnginesConfiguration;
    }

    public function loadTwigEngine(FiddleAgent $agent)
    {
        $fiddle = $agent->getFiddle();
        if (is_null($fiddle))
        {
            throw new \LogicException("You should load a fiddle before trying to prepare its twig engine.");
        }

        $version = $fiddle->getConfig()->getTwigVersion();

        $this->logger->debug("Loading Twig Engine version: {$version}\n");
        $engine = $this->findRightEngine($version);
        if (is_null($engine))
        {
            $agent->addError(Error::E_ENGINE_NOT_FOUND, array ('Version' => $version));
            throw new StopExecutionException();
        }

        $this->logger->debug(sprintf("Twig Engine %s loaded successfully.", get_class($engine)));
        $agent->setEngine($engine);
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
                if (!array_key_exists('version', $tag))
                {
                    continue;
                }
                if (strcmp(strtolower($expectedVersion), strtolower($tag['version'])) == 0)
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
        if (!($service instanceof TwigEngineInterface))
        {
            throw new \LogicException("The Twig Engine version {$expectedVersion} has been found, but does not implement the TwigEngineInterface interface.");
        }
        return $service;
    }

    public function render()
    {
        // todo
    }

}
