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

    public function recoverFiddle(FiddleAgent $agent)
    {
        $this->environmentManager->checkFiddleEnvironmentAvailability($agent);

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

    public function saveResults(FiddleAgent $agent)
    {
        $sharedMemory = $agent->getSharedMemory();

        if (is_null($sharedMemory))
        {
            return $this;
        }

        $this->logger->debug("Storing Fiddle's results in shared memory.");

        $result = new Result();
        $result->setRendered($agent->getRendered());
        $result->setCompiled($agent->getCompiled());
        $result->setErrors($agent->getErrors());

        $sharedMemory->result = $result;
        $sharedMemory->finish_tm = time();

        return $this;
    }

}
