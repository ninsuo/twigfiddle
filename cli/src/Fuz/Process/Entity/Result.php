<?php

namespace Fuz\Process\Entity;

class Result
{
    protected $context;
    protected $rendered;
    protected $compiled;
    protected $errors;

    public function setContext(array $context)
    {
        $this->context = $context;

        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }

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

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
