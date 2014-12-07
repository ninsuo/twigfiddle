<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\FileSystem;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\Agent\FiddleAgent;

class ExecuteManager extends BaseService
{

    protected $fileSystem;
    protected $environmentManager;
    protected $engineManager;
    protected $contextManager;
    protected $templateManager;
    protected $fiddleConfiguration;

    public function __construct(FileSystem $fileSystem, EnvironmentManager $environmentManager,
       EngineManager $engineManager, ContextManager $contextManager, TemplateManager $templateManager,
       array $fiddleConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->environmentManager = $environmentManager;
        $this->engineManager = $engineManager;
        $this->contextManager = $contextManager;
        $this->templateManager = $templateManager;
        $this->fiddleConfiguration = $fiddleConfiguration;
    }

    public function executeFiddle(FiddleAgent $agent)
    {
        $this->checkNoTwigEnvironmentLoaded();
        $this->environmentManager->checkFiddleEnvironmentAvailability($agent);

        $engine = $this->engineManager->getEngineFromAgent($agent);
        $sourceDirectory = $agent->getSourceDirectory();
        $cacheDirectory = $this->createCacheDirectory($agent);
        $context = $this->contextManager->getContextFromAgent($agent);
        $mainTemplate = $this->templateManager->getMainTemplateFromAgent($agent);

        $this->logger->debug("Rendering fiddle's main template: {$mainTemplate}");
        try
        {
            $rendered = $engine->render($sourceDirectory, $cacheDirectory, $mainTemplate, $context);
            $this->logger->debug("Fiddle's rendered result: {$rendered}");
        }
        catch (\Exception $ex)
        {
            $agent->addError(Error::E_EXECUTION_FAILURE, array ("Exception" => $ex));
            throw new StopExecutionException();
        }

        $agent->setRendered($rendered);
        return $this;
    }

    protected function checkNoTwigEnvironmentLoaded()
    {
        if (class_exists("\Twig_Environment"))
        {
            throw new \LogicException("A twig environment has already been loaded.");
        }
        return $this;
    }

    protected function createCacheDirectory(FiddleAgent $agent)
    {
        $dir = $agent->getDirectory();
        $cacheDirectory = $dir . DIRECTORY_SEPARATOR . $this->fiddleConfiguration['compiled_dir'];
        $this->logger->debug("Creating cache directory: {$cacheDirectory}");
        $this->fileSystem->mkdir($cacheDirectory);
        return $cacheDirectory;
    }

}
