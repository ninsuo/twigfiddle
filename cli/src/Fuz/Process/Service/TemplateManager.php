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
use Fuz\Process\Agent\FiddleAgent;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Framework\Service\FileSystem;

class TemplateManager extends BaseService
{

    protected $fileSystem;
    protected $fiddleConfiguration;
    protected $templatesConfiguration;

    public function __construct(FileSystem $fileSystem, array $fiddleConfiguration, array $templatesConfiguration)
    {
        $this->fileSystem = $fileSystem;
        $this->fiddleConfiguration = $fiddleConfiguration;
        $this->templatesConfiguration = $templatesConfiguration;
    }

    public function prepareTemplates(FiddleAgent $agent)
    {
        $fiddle = $agent->getFiddle();
        if (is_null($fiddle))
        {
            throw new \LogicException("You should load a fiddle before trying to prepare its templates.");
        }
        $templates = $this->validateAndSortTemplates($agent);
        $this->writeTemplates($agent, $templates);
        return $this;
    }

    protected function validateAndSortTemplates(FiddleAgent $agent)
    {
        $collection = $agent->getFiddle()->getTemplates();
        if (count($collection) == 0)
        {
            $agent->addError(Error::E_NO_TEMPLATE);
            throw new StopExecutionException();
        }

        $isMain = 0;
        foreach ($collection as $template)
        {
            $isMain += (int) $template->isMain();
        }
        if ($isMain == 0)
        {
            $agent->addError(Error::E_NO_MAIN_TEMPLATE);
            throw new StopExecutionException();
        }
        else if ($isMain >= 2)
        {
            $agent->addError(Error::E_SEVERAL_MAIN_TEMPLATES);
            throw new StopExecutionException();
        }

        $templates = $collection->toArray();
        usort($templates, function($a, $b)
        {
            return $a->isMain() ? -1 : 1;
        });

        return $templates;
    }

    public function writeTemplates(FiddleAgent $agent, array $templates)
    {
        $dir = $agent->getDirectory() . DIRECTORY_SEPARATOR . $this->fiddleConfiguration['templates_dir'];
        $this->logger->debug("Creating template directory: {$dir}");
        $this->fileSystem->mkdir($dir);
        $files = array ();
        foreach ($templates as $template)
        {
            $filename = $template->getFilename();
            if (!preg_match("/{$this->templatesConfiguration['validation']}/", $filename))
            {
                $agent->addError(Error::E_INVALID_TEMPLATE_NAME, array ('name' => $filename));
                throw new StopExecutionException();
            }

            $file = $dir . DIRECTORY_SEPARATOR . $filename;
            $this->logger->debug("Writing template: {$file}.");
            if (@file_put_contents($file, $template->getContent()) === false)
            {
                $agent->addError(Error::E_CANNOT_WRITE_TEMPLATE, array ('file' => $file));
                throw new StopExecutionException();
            }
            $files[] = $file;
        }
        $agent->setTemplates($files);
        return $this;
    }

    public function getMainTemplateFromAgent(FiddleAgent $agent)
    {
        $templates = $agent->getTemplates();
        if (is_null($templates))
        {
            throw new \LogicException("Templates have not been generated in this fiddle.");
        }
        return reset($templates);
    }

}
