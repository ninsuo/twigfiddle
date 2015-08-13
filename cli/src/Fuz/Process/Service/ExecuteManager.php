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
        $mainTemplate = $this->templateManager->getMainTemplateFromAgent($agent);
        $context = $this->contextManager->getContextFromAgent($agent);

        $this->logger->debug("Rendering fiddle's main template: {$mainTemplate}");
        try {
            $rendered = $engine->render($sourceDirectory, $cacheDirectory, $mainTemplate, $context);
            $this->logger->debug("Fiddle's rendered result: {$rendered}");
        } catch (\Exception $ex) {
            $this->treatError($agent, $ex);
            throw new StopExecutionException();
        }

        $agent->setRendered($rendered);

        return $this;
    }

    protected function checkNoTwigEnvironmentLoaded()
    {
        if (class_exists("\Twig_Environment")) {
            throw new \LogicException('A twig environment has already been loaded.');
        }

        return $this;
    }

    protected function createCacheDirectory(FiddleAgent $agent)
    {
        $dir = $agent->getDirectory();
        $cacheDirectory = $dir.DIRECTORY_SEPARATOR.$this->fiddleConfiguration['compiled_dir'];
        $this->logger->debug("Creating cache directory: {$cacheDirectory}");
        $this->fileSystem->mkdir($cacheDirectory);

        return $cacheDirectory;
    }

    /**
     * We cannot catch all kind of exceptions using a normal way
     * as old Twig versions do not distinguish Twig errors and
     * just thrown \Twig_Error.
     *
     * @param FiddleAgent $agent
     * @param \Exception  $ex
     *
     * @return \Fuz\Process\Service\ExecuteManager
     */
    protected function treatError(FiddleAgent $agent, \Exception $ex)
    {
        $no = null;

        $this->logger->log(200, 'xxx', array('ex' => $ex));

        switch (get_class($ex)) {
            case 'Twig_Error_Loader':
                $no = Error::E_TWIG_LOADER_ERROR;
                break;
            case 'Twig_Error_Syntax':
                $no = Error::E_TWIG_SYNTAX_ERROR;
                break;
            case 'Twig_Error_Runtime':
                $no = Error::E_TWIG_RUNTIME_ERROR;
                break;
            default:
                $no = Error::E_EXECUTION_FAILURE;
                break;
        }

        $agent->addError($no, $ex);

        return $this;
    }
}
