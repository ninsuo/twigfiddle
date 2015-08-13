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

class CompiledManager extends BaseService
{
    protected $fileSystem;
    protected $environmentManager;
    protected $fiddleConfiguration;

    public function __construct(FileSystem $fileSystem, EnvironmentManager $environmentManager,
       array $fiddleConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->environmentManager = $environmentManager;
        $this->fiddleConfiguration = $fiddleConfiguration;
    }

    public function extractCompiledFiles(FiddleAgent $agent)
    {
        $this->environmentManager->checkFiddleEnvironmentAvailability($agent);

        $dir = $agent->getDirectory();
        $cacheDirectory = $dir.DIRECTORY_SEPARATOR.$this->fiddleConfiguration['compiled_dir'];
        $this->logger->debug("Extracting compiled files in: $cacheDirectory");

        $compiled = array();
        $directoryIterator = new \RecursiveDirectoryIterator($cacheDirectory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $recursiveIterator = new \RecursiveIteratorIterator($directoryIterator);
        foreach ($recursiveIterator as $fileinfo) {
            $compiledFile = $fileinfo->getRealPath();
            $content = file_get_contents($compiledFile);
            $template = $agent->getEngine()->extractTemplateName($content);

            if (is_null($template)) {
                $agent->addError(Error::E_UNKNOWN_COMPILED_FILE, array('file' => $compiledFile));
            }

            if (!in_array($template, array_map('basename', $agent->getTemplates()))) {
                $agent->addError(Error::E_UNEXPECTED_COMPILED_FILE, array('file' => $compiledFile));
            }

            $compiled[$template] = $content;
            $this->logger->debug("Extracted {$template} from {$compiledFile}");
        }

        $orderedCompiled = array();
        foreach ($agent->getTemplates() as $templatePath) {
            $template = basename($templatePath);
            if (array_key_exists($template, $compiled)) {
                $orderedCompiled[$template] = $compiled[$template];
            }
        }

        $agent->setCompiled($orderedCompiled);

        return $this;
    }
}
