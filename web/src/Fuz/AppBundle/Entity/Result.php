<?php

namespace Fuz\AppBundle\Entity;

use Fuz\Process\Entity\Result as FiddleResult;

/**
 * Entity used to contain fiddle's result
 *
 * @See Fuz\AppBundle\Service\RunFiddle
 */
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

