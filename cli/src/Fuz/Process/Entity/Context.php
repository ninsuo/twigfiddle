<?php

namespace Fuz\Process\Entity;

use Fuz\AppBundle\Entity\Fiddle;
use Fuz\Process\Entity\Error;

class Context
{

    protected $environmentId;
    protected $fiddle;
    protected $errors = array ();

    public function __construct($environmentId)
    {
        $this->environmentId = $environmentId;
    }

    public function getEnvironmentId()
    {
        return $this->environmentId;
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

}
