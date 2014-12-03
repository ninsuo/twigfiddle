<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\FileSystem;

class Debug extends BaseService
{

    protected $fileSystem;
    protected $debugConfiguration;

    public function __construct(FileSystem $fileSystem, array $debugConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->debugConfiguration = $debugConfiguration;
    }

    public function backupIfDebugRequired($runner)
    {
        $this->fileSystem->removeOldFilesAndDirectoriesRecursivly(
           $this->debugConfiguration['directory'], $this->debugConfiguration['expiry'], 'hours');

    }

}
