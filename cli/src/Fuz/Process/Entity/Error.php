<?php

namespace Fuz\Process\Entity;

class Error
{

    const G_GENERAL = 'GENERAL';
    const G_ENVIRONMENT = 'ENVIRONMENT';
    const G_CONTEXT = 'CONTEXT';
    const G_TEMPLATE = 'TEMPLATE';
    const G_ENGINE = 'ENGINE';
    const G_EXECUTION = 'EXECUTION';

    const E_UNKNOWN = 'UNKNOWN';
    const E_UNEXPECTED = 'UNEXPECTED';
    const E_TIMEOUT = 'TIMEOUT';
    const E_INVALID_ENVIRONMENT_ID = 'INVALID_ENVIRONMENT_ID';
    const E_UNEXISTING_ENVIRONMENT_ID = 'UNEXISTING_ENVIRONMENT_ID';
    const E_UNEXISTING_SHARED_MEMORY = 'UNEXISTING_SHARED_MEMORY';
    const E_UNREADABLE_SHARED_MEMORY = 'UNREADABLE_SHARED_MEMORY';
    const E_FIDDLE_ALREADY_RUN = 'FIDDLE_ALREADY_RUN';
    const E_FIDDLE_NOT_STORED = 'FIDDLE_NOT_STORED';
    const E_UNKNOWN_CONTEXT_FORMAT = 'UNKNOWN_CONTEXT_FORMAT';
    const E_INVALID_CONTEXT_SYNTAX = 'INVALID_CONTEXT_SYNTAX';
    const E_NO_TEMPLATE = 'NO_TEMPLATE';
    const E_NO_MAIN_TEMPLATE = 'NO_MAIN_TEMPLATE';
    const E_SEVERAL_MAIN_TEMPLATES = 'SEVERAL_MAIN_TEMPLATES';
    const E_INVALID_TEMPLATE_NAME = 'INVALID_TEMPLATE_NAME';
    const E_CANNOT_WRITE_TEMPLATE = 'CANNOT_WRITE_TEMPLATE';
    const E_ENGINE_NOT_FOUND = 'ENGINE_NOT_FOUND';
    const E_EXECUTION_FAILURE = 'EXECUTION_FAILURE';
    const E_UNKNOWN_COMPILED_FILE = 'UNKNOWN_COMPILED_FILE';
    const E_UNEXPECTED_COMPILED_FILE = 'UNEXPECTED_COMPILED_FILE';

    protected $no;
    protected $group;
    protected $message;
    protected $context;
    protected $public = true;
    protected $debug = false;
    protected $caller;

    public function getNo()
    {
        return $this->no;
    }

    public function setNo($no)
    {
        $this->no = $no;
        return $this;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext(array $context)
    {
        $this->context = $context;
        return $this;
    }

    public function isPublic()
    {
        return $this->isPublic;
    }

    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function isDebug()
    {
        return $this->isDebug;
    }

    public function setIsDebug($isDebug)
    {
        $this->isDebug = $isDebug;
        return $this;
    }

    public function getCaller()
    {
        return $this->caller;
    }

    public function setCaller($caller)
    {
        $this->caller = $caller;
        return $this;
    }

}
