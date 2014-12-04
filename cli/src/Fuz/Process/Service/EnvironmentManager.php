<?php

namespace Fuz\Process\Service;

use Fuz\Component\SharedMemory\SharedMemory;
use Fuz\Component\SharedMemory\Storage\StorageFile;
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
    protected $fiddleConfiguration;

    public function __construct(FileSystem $fileSystem, ContextManager $contextManager, array $debugConfiguration,
       array $environmentConfiguration, array $fiddleConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->contextManager = $contextManager;
        $this->debugConfiguration = $debugConfiguration;
        $this->environmentConfiguration = $environmentConfiguration;
        $this->fiddleConfiguration = $fiddleConfiguration;
    }

    public function recoverFiddle()
    {
        $this->cleanExpiredEnvironments();

        $context = $this->contextManager->getContext();
        $this->validateEnvironmentId($context);
        $this->deduceEnvironmentDirectory($context);
        $this->loadSharedMemory($context);
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

        $this->logger->debug("Environment's path: {$realPath}");

        $context->setDirectory($realPath);
    }

    public function cleanExpiredEnvironments()
    {
        $directory = $this->environmentConfiguration['directory'];
        $timestamp = strtotime("-{$this->environmentConfiguration['expiry']} hours");
        $elements = $this->fileSystem->getFilesAndDirectoriesOlderThan($directory, $timestamp);
        $this->fileSystem->remove($elements);
        $this->logger->debug(sprintf("Cleaned expired environments: %d environments removed.", count($elements)));
    }

    public function loadSharedMemory(Context $context)
    {
        $sharedFile = $context->getDirectory() . DIRECTORY_SEPARATOR . $this->fiddleConfiguration['file'];
        $this->logger->debug("Shared memory path: {$sharedFile}");

        if (!is_file($sharedFile))
        {
            $this->contextManager->addError(Error::E_UNEXISTING_SHARED_MEMORY, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }

        if (!is_readable($sharedFile))
        {
            $this->contextManager->addError(Error::E_UNREADABLE_SHARED_MEMORY, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }

        $storage = new StorageFile($sharedFile);
        $sharedMemory = new SharedMemory($storage);

        $sharedMemory->lock();

        if (!is_null($sharedMemory->begin_tm))
        {
            $sharedMemory->unlock();
            $this->contextManager->addError(Error::E_FIDDLE_ALREADY_RUN, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }
        $sharedMemory->begin_tm = time();

        $fiddle = $sharedMemory->fiddle;
        if (is_null($fiddle))
        {
            $sharedMemory->unlock();
            $this->contextManager->addError(Error::E_FIDDLE_NOT_STORED, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }

        $sharedMemory->unlock();

        $context->setFiddle($fiddle);
        $context->setSharedMemory($sharedMemory);
    }

}
