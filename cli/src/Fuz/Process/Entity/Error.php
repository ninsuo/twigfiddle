<?php

namespace Fuz\Process\Entity;

class Error
{

    const GROUP_GENERAL = 0;
    const E_UNKNOWN = 0;
    const E_UNEXPECTED = 1;

    protected $errno;
    protected $group;
    protected $errstr;
    protected $public = true;
    protected $debug = false;
    protected $caller;

    public function getErrno()
    {
        return $this->errno;
    }

    public function setErrno($errno)
    {
        $this->errno = $errno;
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

    public function getErrstr()
    {
        return $this->errstr;
    }

    public function setErrstr($errstr)
    {
        $this->errstr = $errstr;
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
