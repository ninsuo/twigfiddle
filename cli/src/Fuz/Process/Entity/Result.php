<?php

namespace Fuz\Process\Entity;

class Result
{

    protected $renderedResult;
    protected $compiledFiles;

    public function setRenderedResult($renderedResult)
    {
        $this->renderedResult = $renderedResult;
        return $this;
    }

    public function getRenderedResult()
    {
        return $this->renderedResult;
    }

    public function setCompiledFiles(array $compiledFiles)
    {
        $this->compiledFiles = $compiledFiles;
        return $this;
    }

    public function getCompiledFiles()
    {
        return $this->compiledFiles;
    }

}