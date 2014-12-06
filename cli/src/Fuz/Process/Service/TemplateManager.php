<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Context;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\Helper\ContextHelper;
use Fuz\Framework\Service\FileSystem;

class TemplateManager extends BaseService
{

    protected $contextHelper;
    protected $fileSystem;
    protected $fiddleConfiguration;
    protected $templatesConfiguration;

    public function __construct(ContextHelper $contextHelper, FileSystem $fileSystem, array $fiddleConfiguration, array $templatesConfiguration)
    {
        $this->contextHelper = $contextHelper;
        $this->fileSystem = $fileSystem;
        $this->fiddleConfiguration = $fiddleConfiguration;
        $this->templatesConfiguration = $templatesConfiguration;
    }

    public function prepareTemplates()
    {
        $context = $this->contextHelper->getContext();
        $fiddle = $context->getFiddle();
        if (is_null($fiddle))
        {
            throw new \LogicException("You should load a fiddle before trying to prepare its templates.");
        }
        $templates = $this->validateAndSortTemplates($context);
        $this->writeTemplates($context, $templates);
    }

    public function validateAndSortTemplates($context)
    {
        $collection = $context->getFiddle()->getTemplates();
        if (count($collection) == 0)
        {
            $this->contextHelper->addError(Error::E_NO_TEMPLATE);
            throw new StopExecutionException();
        }

        $isMain = 0;
        foreach ($collection as $template)
        {
            $isMain += (int) $template->isMain();
        }
        if ($isMain == 0)
        {
            $this->contextHelper->addError(Error::E_NO_MAIN_TEMPLATE);
            throw new StopExecutionException();
        }
        else if ($isMain >= 2)
        {
            $this->contextHelper->addError(Error::E_SEVERAL_MAIN_TEMPLATES);
            throw new StopExecutionException();
        }

        $templates = $collection->toArray();
        usort($templates,
           function($a, $b)
        {
            return $a->isMain() ? -1 : 1;
        });

        return $templates;
    }

    public function writeTemplates(Context $context, array $templates)
    {
        $dir = $context->getDirectory() . DIRECTORY_SEPARATOR . $this->fiddleConfiguration['templates_dir'];
        $this->fileSystem->mkdir($dir);
        $files = array();
        foreach ($templates as $template)
        {
            $filename = $template->getFilename();
            if (!preg_match("/{$this->templatesConfiguration['validation']}/", $filename))
            {
                $this->contextHelper->addError(Error::E_INVALID_TEMPLATE_NAME, array ('Name' => $filename));
                throw new StopExecutionException();
            }

            $file = $dir . DIRECTORY_SEPARATOR . $filename;
            $this->logger->debug("Writing template: {$file}.");
            if (@file_put_contents($file, $template->getContent()) === false)
            {
                $this->contextHelper->addError(Error::E_CANNOT_WRITE_TEMPLATE, array ('File' => $file));
                throw new StopExecutionException();
            }
            $files[] = $file;
        }
        $context->setTemplates($files);
    }

}
