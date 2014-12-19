<?php

namespace Fuz\AppBundle\Entity;

use Fuz\Process\Entity\Result as FiddleResult;

class Result
{

    protected $result = null;
    protected $duration = null;

    public function setResult(FiddleResult $result)
    {
        $this->result = $result;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function getDuration()
    {
        return $this->duration;
    }

}

