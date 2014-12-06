<?php

namespace Fuz\Process\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\Helper\ContextHelper;
use Fuz\Process\TwigEngine\TwigEngineInterface;

class EngineManager extends BaseService
{

    protected $container;
    protected $contextHelper;
    protected $fiddleConfiguration;
    protected $twigEnginesConfiguration;

    public function __construct(ContainerInterface $container, ContextHelper $contextHelper, array $fiddleConfiguration,
       array $twigEnginesConfiguration)
    {
        $this->container = $container;
        $this->contextHelper = $contextHelper;
        $this->fiddleConfiguration = $fiddleConfiguration;
        $this->twigEnginesConfiguration = $twigEnginesConfiguration;
    }

    public function loadEngine()
    {
        $context = $this->contextHelper->getContext();
        $fiddle = $context->getFiddle();
        if (is_null($fiddle))
        {
            throw new \LogicException("You should load a fiddle before trying to prepare its twig engine.");
        }

        $version = $fiddle->getConfig()->getTwigVersion();

        $this->logger->debug("Loading Twig Engine version: {$version}\n");
        $engine = $this->findRightEngine($version);
        if (is_null($engine))
        {
            $this->contextHelper->addError(Error::E_ENGINE_NOT_FOUND, array ('Version' => $version));
            throw new StopExecutionException();
        }

        $this->logger->debug(sprintf("Twig Engine %s loaded successfully.", get_class($engine)));
        $context->setEngine($engine);
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
