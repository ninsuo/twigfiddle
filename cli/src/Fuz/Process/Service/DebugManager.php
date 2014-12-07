<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\FileSystem;
use Fuz\Process\Agent\FiddleAgent;

class DebugManager extends BaseService
{

    protected $fileSystem;
    protected $debugConfiguration;
    protected $environmentConfiguration;

    public function __construct(FileSystem $fileSystem, array $debugConfiguration, array $environmentConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->debugConfiguration = $debugConfiguration;
        $this->environmentConfiguration = $environmentConfiguration;
    }

    public function backupIfDebugRequired(FiddleAgent $agent)
    {
        if (!$this->debugConfiguration['allowed'])
        {
            return $this;
        }

        $this->cleanExpiredDebugFiles();

        if (!$agent->isDebug())
        {
            return $this;
        }

        $requiresDebug = 0;
        foreach ($agent->getErrors() as $error)
        {
            $requiresDebug += (int) $error->isDebug();
        }

        if ($requiresDebug)
        {
            $this->logger->warning("This fiddle requires developers attention, copying it to the debug directory.");
            $this->copyFiddleToDebugDirectory($agent);
        }
        else
        {
            $this->logger->debug("This fiddle does not require developers attention.");
        }
    }

    public function cleanExpiredDebugFiles()
    {
        $directory = $this->debugConfiguration['directory'];
        $timestamp = strtotime("-{$this->debugConfiguration['expiry']} hours");
        $elements = $this->fileSystem->getFilesAndDirectoriesOlderThan($directory, $timestamp);
        $this->fileSystem->remove($elements);
        $this->logger->debug(sprintf("Cleaned expired debug environments: %d environments removed.", count($elements)));
    }

    protected function copyFiddleToDebugDirectory(FiddleAgent $agent)
    {
        $environmentId = $agent->getEnvironmentId();
        if (preg_match("/{$this->environmentConfiguration['validation']}/", $environmentId))
        {
            $source = $this->environmentConfiguration['directory'] . DIRECTORY_SEPARATOR . $environmentId;
            $target = $this->debugConfiguration['directory'] . DIRECTORY_SEPARATOR . $environmentId;
            $this->logger->debug("Copying {$source} to {$target}");
            $this->fileSystem->copyDirectory($source, $target);
            @file_put_contents($target . DIRECTORY_SEPARATOR . $this->debugConfiguration['context_file'], serialize($agent));
        }
    }

}
