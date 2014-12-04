<?php

namespace Fuz\Process\Entity;

class Error
{

    const G_GENERAL = 0;
    const G_ENVIRONMENT = 1;

    const E_UNKNOWN = 0;
    const E_UNEXPECTED = 1;
    const E_INVALID_ENVIRONMENT_ID = 2;
    const E_UNEXISTING_ENVIRONMENT_ID = 3;
    const E_UNEXISTING_SHARED_MEMORY = 4;
    const E_UNREADABLE_SHARED_MEMORY = 5;
    const E_FIDDLE_ALREADY_RUN = 6;
    const E_FIDDLE_NOT_STORED = 7;

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
