<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\StringLoader;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;
use Fuz\Process\Agent\FiddleAgent;

class ContextManager extends BaseService
{

    protected $stringLoader;

    public function __construct(StringLoader $stringLoader)
    {
        $this->stringLoader = $stringLoader;
    }

    public function extractContext(FiddleAgent $agent)
    {
        $fiddle = $agent->getFiddle();
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
            $agent->addError(Error::E_UNKNOWN_CONTEXT_FORMAT, array ('Format' => $format));
            throw new StopExecutionException();
        }
        catch (\LogicException $ex)
        {
            $agent->addError(Error::E_UNEXPECTED, array ('Exception' => $ex));
            throw new StopExecutionException();
        }
        catch (\Exception $ex)
        {
            $agent->addError(Error::E_INVALID_CONTEXT_SYNTAX, array ('Exception' => $ex));
            throw new StopExecutionException();
        }

        $this->logger->debug("Successfully extracted the context.", array ('context' => $array));
        $agent->setContext($array);

        return $this;
    }

    public function getContextFromAgent(FiddleAgent $agent)
    {
        $context = $agent->getContext();
        if (is_null($context))
        {
            throw new \LogicException("Context has not been converted in this fiddle.");
        }
        return $context;
    }

}
