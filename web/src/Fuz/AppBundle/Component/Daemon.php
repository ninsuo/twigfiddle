<?php

namespace Fuz\AppBundle\Component;

use Symfony\Component\HttpKernel\KernelInterface;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * This class intends to run a Linux command as daemon.
 *
 * @Service("app.daemon")
 */
class Daemon
{

    const PHPCLI_DIR = '/usr/bin/php';

    /**
     * @Inject("kernel")
     */
    public $kernel;

    public function exec($command, $arguments = array ())
    {
        foreach ($arguments as $argument)
        {
            $command .= ' ' . escapeshellarg($argument);
        }
        if (php_sapi_name() !== "cli")
        {
            $this->cliize($command);
        }
        else
        {
            $this->daemonize($command);
        }
    }

    public function bind($command, $parameters = array ())
    {
        foreach ($parameters as $placeholder => $value)
        {
            $command = str_replace(":{$placeholder}", escapeshellarg($value));
        }
        return $command;
    }

    protected function cliize($command)
    {
        $console = escapeshellarg($this->kernel->getRootDir() . '/console');
        $escaped_command = escapeshellarg($command);
        exec(self::PHPCLI_DIR . " $console exec:daemon {$escaped_command} >> /dev/null 2>&1 &");
    }

    protected function daemonize($command)
    {
        $parent_pid = pcntl_fork();
        if ($parent_pid == -1)
        {
            throw new \Exception("Failed to fork() !");
        }
        if ($parent_pid)
        {
            return;
        }
        else
        {
            posix_setsid();
            exec("{$command} >> /dev/null 2>&1 &");
        }
    }

}
