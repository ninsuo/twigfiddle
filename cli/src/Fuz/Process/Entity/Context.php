<?php

namespace Fuz\Process\Entity;

use Fuz\Component\SharedMemory\SharedMemory;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\Process\Entity\Error;

class Context
{

    protected $environmentId;
    protected $isDebug;
    protected $directory;
    protected $fiddle;
    protected $errors = array ();
    protected $sharedMemory;

    public function __construct($environmentId, $isDebug = false)
    {
        $this->environmentId = $environmentId;
        $this->isDebug = $isDebug;
    }

    public function getEnvironmentId()
    {
        return $this->environmentId;
    }

    public function isDebug()
    {
        return $this->isDebug;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function setFiddle(Fiddle $fiddle)
    {
        $this->fiddle = $fiddle;
        return $this;
    }

    public function getFiddle()
    {
        return $this->fiddle;
    }

    public function addError(Error $error)
    {
        $this->errors[] = $error;
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setSharedMemory(SharedMemory $sharedMemory)
    {
        $this->sharedMemory = $sharedMemory;
        return $this;
    }

    public function getSharedMemory()
    {
        return $this->sharedMemory;
    }

}
