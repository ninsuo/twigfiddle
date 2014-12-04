<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\FileSystem;
use Fuz\Process\Entity\Error;
use Fuz\Process\Entity\Context;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\Service\ContextManager;

class EnvironmentManager extends BaseService
{

    protected $fileSystem;
    protected $contextManager;
    protected $debugConfiguration;
    protected $environmentConfiguration;

    public function __construct(FileSystem $fileSystem, ContextManager $contextManager, array $debugConfiguration,
       array $environmentConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->contextManager = $contextManager;
        $this->debugConfiguration = $debugConfiguration;
        $this->environmentConfiguration = $environmentConfiguration;
    }

    public function recoverFiddle()
    {
        $this->cleanExpiredEnvironments();
        $context = $this->contextManager->getContext();
        $this->validateEnvironmentId($context);
        $this->deduceEnvironmentDirectory($context);

        // todo


    }

    public function validateEnvironmentId(Context $context)
    {
        $env_id = $context->getEnvironmentId();
        if (!preg_match("/{$this->environmentConfiguration['validation']}/", $env_id))
        {
            $this->contextManager->addError(Error::E_INVALID_ENVIRONMENT_ID, array ('Environment ID' => $env_id));
            throw new StopExecutionException();
        }
    }

    public function deduceEnvironmentDirectory(Context $context)
    {
        $env_id = $context->getEnvironmentId();
        if ($context->isDebug())
        {
            $path = $this->debugConfiguration['directory'] . DIRECTORY_SEPARATOR . $env_id;
        }
        else
        {
            $path = $this->environmentConfiguration['directory'] . DIRECTORY_SEPARATOR . $env_id;
        }

        $realPath = realpath($path);

        if (!is_dir($realPath))
        {
            $this->contextManager->addError(Error::E_UNEXISTING_ENVIRONMENT_ID, array ('Environment ID' => $env_id));
            throw new StopExecutionException();
        }

        $context->setDirectory($realPath);
    }

    public function cleanExpiredEnvironments()
    {
        $this->logger->debug("Cleaning expired environments");
        $directory = $this->environmentConfiguration['directory'];
        $timestamp = strtotime("-{$this->environmentConfiguration['expiry']} hours");
        $elements = $this->fileSystem->getFilesAndDirectoriesOlderThan($directory, $timestamp);
        $this->fileSystem->remove($elements);
    }

}
