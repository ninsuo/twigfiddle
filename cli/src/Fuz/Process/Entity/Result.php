<?php

namespace Fuz\Process\Entity;

class Result
{

    protected $rendered;
    protected $compiled;

    public function setRendered($rendered)
    {
        $this->rendered = $rendered;
        return $this;
    }

    public function getRendered()
    {
        return $this->rendered;
    }

    public function setCompiled(array $compiled)
    {
        $this->compiled = $compiled;
        return $this;
    }

    public function getCompiled()
    {
        return $this->compiled;
    }

}
