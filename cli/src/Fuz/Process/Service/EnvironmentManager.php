<?php

namespace Fuz\Process\Service;

use Fuz\Component\SharedMemory\SharedMemory;
use Fuz\Component\SharedMemory\Storage\StorageFile;
use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\FileSystem;
use Fuz\Process\Entity\Error;
use Fuz\Process\Agent\FiddleAgent;
use Fuz\Process\Exception\StopExecutionException;

class EnvironmentManager extends BaseService
{

    protected $fileSystem;
    protected $debugConfiguration;
    protected $environmentConfiguration;
    protected $fiddleConfiguration;

    public function __construct(FileSystem $fileSystem, array $debugConfiguration, array $environmentConfiguration,
       array $fiddleConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->debugConfiguration = $debugConfiguration;
        $this->environmentConfiguration = $environmentConfiguration;
        $this->fiddleConfiguration = $fiddleConfiguration;
    }

    public function recoverFiddle(FiddleAgent $agent)
    {
        $this->cleanExpiredEnvironments();

        $this
           ->validateEnvironmentId($agent)
           ->deduceEnvironmentDirectory($agent)
           ->loadSharedMemory($agent)
        ;
    }

    public function cleanExpiredEnvironments()
    {
        $directory = $this->environmentConfiguration['directory'];
        $timestamp = strtotime("-{$this->environmentConfiguration['expiry']} hours");
        $elements = $this->fileSystem->getFilesAndDirectoriesOlderThan($directory, $timestamp);
        $this->fileSystem->remove($elements);
        $this->logger->debug(sprintf("Cleaned expired environments: %d environments removed.", count($elements)));
        return $this;
    }

    protected function validateEnvironmentId(FiddleAgent $agent)
    {
        $env_id = $agent->getEnvironmentId();
        if (!preg_match("/{$this->environmentConfiguration['validation']}/", $env_id))
        {
            $agent->addError(Error::E_INVALID_ENVIRONMENT_ID, array ('Environment ID' => $env_id));
            throw new StopExecutionException();
        }
        return $this;
    }

    protected function deduceEnvironmentDirectory(FiddleAgent $agent)
    {
        $env_id = $agent->getEnvironmentId();
        if ($agent->isDebug())
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
            $agent->addError(Error::E_UNEXISTING_ENVIRONMENT_ID, array ('Environment ID' => $env_id));
            throw new StopExecutionException();
        }

        $this->logger->debug("Environment's path: {$realPath}");

        $agent->setDirectory($realPath);
        return $this;
    }

    protected function loadSharedMemory(FiddleAgent $agent)
    {
        $sharedFile = $agent->getDirectory() . DIRECTORY_SEPARATOR . $this->fiddleConfiguration['file'];
        $this->logger->debug("Shared memory path: {$sharedFile}");

        if (!is_file($sharedFile))
        {
            $agent->addError(Error::E_UNEXISTING_SHARED_MEMORY, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }

        if (!is_readable($sharedFile))
        {
            $agent->addError(Error::E_UNREADABLE_SHARED_MEMORY, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }

        $storage = new StorageFile($sharedFile);
        $sharedMemory = new SharedMemory($storage);

        $sharedMemory->lock();

        if (!is_null($sharedMemory->begin_tm))
        {
            $sharedMemory->unlock();
            $agent->addError(Error::E_FIDDLE_ALREADY_RUN, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }
        $sharedMemory->begin_tm = time();

        $fiddle = $sharedMemory->fiddle;
        if (is_null($fiddle))
        {
            $sharedMemory->unlock();
            $agent->addError(Error::E_FIDDLE_NOT_STORED, array ('Shared File' => $sharedFile));
            throw new StopExecutionException();
        }

        $sharedMemory->unlock();

        $agent->setFiddle($fiddle);
        $agent->setSharedMemory($sharedMemory);
        return $this;
    }

}
