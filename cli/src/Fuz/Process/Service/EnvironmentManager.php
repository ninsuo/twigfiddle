<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\FileSystem;
use Fuz\Process\Agent\FiddleAgent;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;

class EnvironmentManager extends BaseService
{
    protected $fileSystem;
    protected $debugConfiguration;
    protected $environmentConfiguration;
    protected $fiddleConfiguration;

    public function __construct(FileSystem $fileSystem, array $debugConfiguration, array $environmentConfiguration,
       array $fiddleConfiguration)
    {
        $this->fileSystem               = $fileSystem;
        $this->debugConfiguration       = $debugConfiguration;
        $this->environmentConfiguration = $environmentConfiguration;
        $this->fiddleConfiguration      = $fiddleConfiguration;
    }

    public function prepareEnvironment(FiddleAgent $agent)
    {
        $this->cleanExpiredEnvironments();

        $this
           ->validateEnvironmentId($agent)
           ->deduceEnvironmentDirectory($agent)
        ;
    }

    public function cleanExpiredEnvironments()
    {
        $directory = $this->environmentConfiguration['directory'];
        $timestamp = strtotime("-{$this->environmentConfiguration['expiry']} hours");
        $elements  = $this->fileSystem->getFilesAndDirectoriesOlderThan($directory, $timestamp);
        unset($elements[array_search('.gitkeep', $elements)]);
        $this->fileSystem->remove($elements);
        $this->logger->debug(sprintf('Cleaned expired environments: %d environments removed.', count($elements)));

        return $this;
    }

    public function checkFiddleEnvironmentAvailability(FiddleAgent $agent)
    {
        $dir = $agent->getDirectory();
        if ((is_null($dir)) || (!is_dir($dir)) || (!is_writable($dir))) {
            throw new \LogicException("The fiddle's environment does not seem to be ready.");
        }

        return $dir;
    }

    protected function validateEnvironmentId(FiddleAgent $agent)
    {
        $env_id = $agent->getEnvironmentId();
        if (!preg_match("/{$this->environmentConfiguration['validation']}/", $env_id)) {
            $agent->addError(Error::E_INVALID_ENVIRONMENT_ID, ['environment id' => $env_id]);
            throw new StopExecutionException();
        }

        return $this;
    }

    protected function deduceEnvironmentDirectory(FiddleAgent $agent)
    {
        $env_id = $agent->getEnvironmentId();
        if ($agent->isDebug()) {
            $path = $this->debugConfiguration['directory'].DIRECTORY_SEPARATOR.$env_id;
        } else {
            $path = $this->environmentConfiguration['directory'].DIRECTORY_SEPARATOR.$env_id;
        }

        $realPath = realpath($path);

        if (!is_dir($realPath)) {
            $agent->addError(Error::E_UNEXISTING_ENVIRONMENT_ID, ['environment id' => $env_id]);
            throw new StopExecutionException();
        }

        $this->logger->debug("Environment's path: {$realPath}");

        $agent->setDirectory($realPath);

        return $this;
    }
}
