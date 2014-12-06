<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\StringLoader;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\Helper\ContextHelper;

class ContextManager extends BaseService
{

    protected $contextHelper;
    protected $stringLoader;

    public function __construct(ContextHelper $contextHelper, StringLoader $stringLoader)
    {
        $this->contextHelper = $contextHelper;
        $this->stringLoader = $stringLoader;
    }

    public function extractContext()
    {
        $context = $this->contextHelper->getContext();
        $fiddle = $context->getFiddle();
        if (is_null($fiddle))
        {
            throw new \LogicException("You should load a fiddle before trying to extract its context.");
        }

        $content = $fiddle->getContext()->getContent();
        $format = $fiddle->getContext()->getFormat();

        $this->logger->debug("Extracting Twig context from format: {$format}.");
        try
        {
            $array = $this->stringLoader->load($content, $format);
        }
        catch (\InvalidArgumentException $ex)
        {
            $this->contextHelper->addError(Error::E_UNKNOWN_CONTEXT_FORMAT, array('Format' => $format));
            throw new StopExecutionException();
        }
        catch (\LogicException $ex)
        {
            $this->contextHelper->addError(Error::E_UNEXPECTED, array('Exception' => $ex));
            throw new StopExecutionException();
        }
        catch (\Exception $ex)
        {
            $this->contextHelper->addError(Error::E_INVALID_CONTEXT_SYNTAX, array('Exception' => $ex));
            throw new StopExecutionException();
        }

        $this->logger->debug("Successfully extracted the context.", array('context' => $array));
        $context->setContext($array);
    }

}
