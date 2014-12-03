<?php

namespace Fuz\Framework\Service;

use Symfony\Component\Finder\Finder;
use Fuz\Framework\Base\BaseService;

class FileSystem extends BaseService
{

    public function removeOldFilesAndDirectoriesRecursivly($directory, $expiry, $expiryUnit)
    {
        $this->logger->debug("Cleaning files and directories from {$directory} when they are older than {$expiry} {$expiryUnit}.");

        $finder = new Finder();
        $finder
           ->date("before -{$expiry} {$expiryUnit}")
           ->in($directory)
           ->sort(function(\SplFileInfo $a, \SplFileInfo $b)
           {
               return ($a->isDir() && $b->isDir()) || ($a->isFile() && $b->isFile()) ? 0 : (($a->isDir() && $b->isFile()) ? 1 : -1);
           })
        ;

        $removed = array();
        foreach ($finder as $element)
        {
            $path = $element->getRealPath();
            $status = ($element->isDir() ? @rmdir($path) : @unlink($path)) ? 'success' : 'failure';
            if ($status === 'success')
            {
                $removed[] = $path;
            }
            $this->logger->debug("Removing {$path}: {$status}\n");
        }

        $this->logger->debug(sprintf("Removed %d files and directories.", count($removed)));
        return $removed;
    }

}
