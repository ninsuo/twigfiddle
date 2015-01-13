<?php

namespace Fuz\Framework\Service;

use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Symfony\Component\Filesystem\Exception\IOException;

class FileSystem extends SymfonyFileSystem
{
    public function getFilesAndDirectoriesOlderThan($directory, $timestamp)
    {
        if (!is_dir($directory)) {
            throw new IOException("Directory {$directory} not found.");
        }

        if (!is_readable($directory)) {
            throw new IOException("Directory {$directory} not readable.");
        }

        $list = scandir($directory);
        $return = array();
        foreach ($list as $elem) {
            if (in_array($elem, array('.', '..'))) {
                continue;
            }

            $finfo = new \SplFileInfo($directory.DIRECTORY_SEPARATOR.$elem);
            if ($finfo->isLink()) {
                continue;
            }

            if ($finfo->getMTime() <= $timestamp) {
                $return[] = $finfo->getRealPath();
            }
        }

        return $return;
    }

    public function copyDirectory($source, $target)
    {
        $realSource = realpath($source);
        if ($realSource === false) {
            throw new IOException("Real path of the source directory '{$source}' can't be retreived.");
        }

        if (file_exists($target)) {
            $this->remove($target);
        }

        $this->mkdir($target);
        if (!is_dir($target)) {
            throw new \InvalidArgumentException("Unable to create '{$target}' directory.");
        }

        $directoryIterator = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $this->mkdir($target.DIRECTORY_SEPARATOR.$iterator->getSubPathName());
            } else {
                $this->copy($item, $target.DIRECTORY_SEPARATOR.$iterator->getSubPathName());
            }
        }
    }
}
