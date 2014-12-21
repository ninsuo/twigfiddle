<?php

namespace Fuz\Process\Service;

use Fuz\Component\SharedMemory\SharedMemory;
use Fuz\Component\SharedMemory\Storage\StorageFile;
use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Error;
use Fuz\Process\Entity\Result;
use Fuz\Process\Agent\FiddleAgent;
use Fuz\Process\Exception\StopExecutionException;

class SharedMemoryManager extends BaseService
{

    protected $environmentManager;
    protected $fiddleConfiguration;

    public function __construct(EnvironmentManager $environmentManager, array $fiddleConfiguration)
    {
        $this->environmentManager = $environmentManager;
        $this->fiddleConfiguration = $fiddleConfiguration;
    }

    public function recoverSharedMemory(FiddleAgent $agent, $checkForExistance = true)
    {
        $this->environmentManager->checkFiddleEnvironmentAvailability($agent);

        $sharedFile = $agent->getDirectory() . DIRECTORY_SEPARATOR . $this->fiddleConfiguration['file'];
        $this->logger->debug("Shared memory path: {$sharedFile}");

        if ($checkForExistance)
        {
            if (!is_file($sharedFile))
            {
                $agent->addError(Error::E_UNEXISTING_SHARED_MEMORY, array ('shared file' => $sharedFile));
                throw new StopExecutionException();
            }

            if (!is_readable($sharedFile))
            {
                $agent->addError(Error::E_UNREADABLE_SHARED_MEMORY, array ('shared file' => $sharedFile));
                throw new StopExecutionException();
            }
        }

        $storage = new StorageFile($sharedFile);
        $sharedMemory = new SharedMemory($storage);

        $agent->setStorageName($sharedFile);
        $agent->setSharedMemory($sharedMemory);

        return $this;
    }

    public function recoverFiddle(FiddleAgent $agent)
    {
        $this->recoverSharedMemory($agent);
        $sharedMemory = $agent->getSharedMemory();

        $sharedMemory->lock();

        if ((!$agent->isDebug()) && (!is_null($sharedMemory->begin_tm)))
        {
            $sharedMemory->unlock();
            $agent->addError(Error::E_FIDDLE_ALREADY_RUN, array ('shared file' => $agent->getStorageName()));
            throw new StopExecutionException();
        }
        $sharedMemory->begin_tm = microtime(true);

        $fiddle = $sharedMemory->fiddle;
        if (is_null($fiddle))
        {
            $sharedMemory->unlock();
            $agent->addError(Error::E_FIDDLE_NOT_STORED, array ('shared file' => $agent->getStorageName()));
            throw new StopExecutionException();
        }

        $sharedMemory->unlock();
        $agent->setFiddle($fiddle);

        return $this;
    }

    public function saveResults(FiddleAgent $agent)
    {
        $sharedMemory = $agent->getSharedMemory();

        if (is_null($sharedMemory))
        {
            return $this;
        }

        $this->logger->debug("Storing Fiddle's results in shared memory.");

        $result = new Result();
        $result->setContext($agent->getContext());
        $result->setRendered($agent->getRendered());
        $result->setCompiled($agent->getCompiled());
        $result->setErrors($agent->getErrors());

        $sharedMemory->result = $result;
        $sharedMemory->finish_tm = microtime(true);

        return $this;
    }

}
