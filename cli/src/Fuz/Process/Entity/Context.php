<?php

namespace Fuz\Process\Entity;

use Fuz\Component\SharedMemory\SharedMemory;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\Process\Entity\Error;
use Fuz\Process\TwigEngine\TwigEngineInterface;

class Context
{

    /**
     * Execution's environment ID
     *
     * @var string
     */
    protected $environmentId;

    /**
     * Fiddles taken from debug directory?
     *
     * @var bool
     */
    protected $isDebug;

    /**
     * Execution's environment directory
     *
     * @var string
     */
    protected $directory;

    /**
     * User's fiddle
     *
     * @var Fiddle
     */
    protected $fiddle;

    /**
     * Execution error(s)
     *
     * @var Error[]
     */
    protected $errors = array ();

    /**
     * Variable shared with web application
     *
     * @var SharedMemory
     */
    protected $sharedMemory;

    /**
     * Twig Engine for the right Twig's version
     *
     * @var TwigEngineInterface
     */
    protected $engine;

    /**
     * Fiddle's context converted to array
     *
     * @var mixed[]
     */
    protected $context = array ();

    /**
     * List templates full paths
     *
     * @var string[]
     */
    protected $templates = array ();

    /**
     * Fiddle's result
     *
     * @var string|null
     */
    protected $result;

    /**
     * Compiled templates
     *
     * @var array[]
     */
    protected $compiled = array ();

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

    public function addError($error)
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

    public function setEngine(TwigEngineInterface $engine)
    {
        $this->engine = $engine;
        return $this;
    }

    public function getEngine()
    {
        return $this->engine;
    }

    public function setContext(array $context)
    {
        $this->context = $context;
        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setTemplates(array $templates)
    {
        $this->templates = $templates;
        return $this;
    }

    public function getTemplates()
    {
        return $this->templates;
    }

    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
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
