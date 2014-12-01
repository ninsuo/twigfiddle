<?php

namespace Fuz\Process\Entity;

use Fuz\AppBundle\Entity\Fiddle;
use Fuz\Runner\Entity\Error;

class Runner
{

    protected $fiddle;
    protected $errors = array ();

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
