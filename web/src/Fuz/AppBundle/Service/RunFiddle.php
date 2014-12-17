<?php

namespace Fuz\AppBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Psr\Log\LoggerInterface;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\Component\SharedMemory\Storage\FileStorage;
use Fuz\Component\SharedMemory\SharedMemory;

class RunFiddle
{

    const ENV_NAME_LENGTH = 16;

    protected $logger;
    protected $filesystem;
    protected $localConfig;
    protected $remoteConfig;
    protected $envId;
    protected $envPath;

    public function __construct(LoggerInterface $logger, Filesystem $filesystem,
       ProcessConfiguration $processConfiguration, array $localConfig)
    {
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->localConfig = $localConfig;
        $this->remoteConfig = $processConfiguration->getProcessConfig();
    }

    public function run(Fiddle $fiddle)
    {
        $this
           ->createEnvironment()
           ->createSharedMemory($fiddle)
           ;


        //$this->clearEnvironment();
    }

    protected function createEnvironment()
    {
        $this->logger->debug("Creating a new environment");

        $envRoot = $this->remoteConfig['environment']['directory'];
        if (!is_dir($envRoot))
        {
            throw new IOException("Environment directory {$envRoot} does not exist.");
        }

        $letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890";
        mt_srand(base_convert(uniqid(), 16, 10));
        $base = strlen($letters);
        do
        {
            $env = '';
            for ($i = 0; ($i < self::ENV_NAME_LENGTH); $i++)
            {
               $env .= $letters[mt_rand(0, $base)];
            }
        }
        while (is_dir("{$envRoot}/{$env}"));

        $this->envId = $env;
        $this->envPath = "{$envRoot}/{$env}";
        $this->filesystem->mkdir($this->envPath);
        return $this;
    }

    protected function createSharedMemory(Fiddle $fiddle)
    {

        // shared memory file name on remote config


//        $storage = new StorageFile("{$this->envDir}/{$this->envId}/fiddle.shr");
//        $this->shared = new SharedMemory($storage);
//
//        $this->shared->fiddle = $fiddle;
//        $this->shared->begin_tm = null;
//        $this->shared->finish_tm = null;
//        $this->shared->result = null;


    }

    protected function clearEnvironment()
    {
        $this->filesystem->remove($this->envPath);
        return $this;
    }

}
