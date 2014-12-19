<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Error;

class ErrorManager extends BaseService
{

    protected $errors = array (
            Error::E_UNKNOWN => array (
                    'group' => Error::G_GENERAL,
                    'message' => "An unknwon error occured.",
                    'logger' => 'error',
                    'public' => false,
                    'debug' => true,
            ),
            Error::E_UNEXPECTED => array (
                    'group' => Error::G_GENERAL,
                    'message' => "An unexpected error occured.",
                    'logger' => 'error',
                    'public' => false,
                    'debug' => true,
            ),
            Error::E_TIMEOUT => array (
                    'group' => Error::G_GENERAL,
                    'message' => "Maximum execution timeout was reached.",
                    'logger' => 'info',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_INVALID_ENVIRONMENT_ID => array (
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "The given environment ID does not match with the expected format.",
                    'logger' => 'warning',
                    'public' => false,
                    'debug' => false,
            ),
            Error::E_UNEXISTING_ENVIRONMENT_ID => array(
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "The given environment ID does not have an associated directory.",
                    'logger' => 'warning',
                    'public' => false,
                    'debug' => false,
            ),
            Error::E_UNEXISTING_SHARED_MEMORY => array(
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "The envrionment's shared memory does not exist.",
                    'logger' => 'warning',
                    'public' => false,
                    'debug' => false,
            ),
            Error::E_UNREADABLE_SHARED_MEMORY => array(
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "The envrionment's shared memory is not readable.",
                    'logger' => 'error',
                    'public' => false,
                    'debug' => false,
            ),
            Error::E_FIDDLE_ALREADY_RUN => array(
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "This fiddle has already been started.",
                    'logger' => 'info',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_FIDDLE_NOT_STORED => array(
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "Fiddle not found in the shared object.",
                    'logger' => 'error',
                    'public' => false,
                    'debug' => false,
            ),
            Error::E_UNKNOWN_CONTEXT_FORMAT => array(
                    'group' => Error::G_CONTEXT,
                    'message' => "The given context format is not supported.",
                    'logger' => 'error',
                    'public' => true,
                    'debug' => true,
            ),
            Error::E_INVALID_CONTEXT_SYNTAX => array(
                    'group' => Error::G_CONTEXT,
                    'message' => "Unable to convert the given twig context to an array.",
                    'logger' => 'info',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_NO_TEMPLATE => array(
                    'group' => Error::G_TEMPLATE,
                    'message' => "This fiddle does not contain any template.",
                    'logger' => 'warning',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_NO_MAIN_TEMPLATE => array(
                    'group' => Error::G_TEMPLATE,
                    'message' => "This fiddle does not have a main template.",
                    'logger' => 'warning',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_SEVERAL_MAIN_TEMPLATES => array(
                    'group' => Error::G_TEMPLATE,
                    'message' => "This fiddle has several main templates.",
                    'logger' => 'warning',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_INVALID_TEMPLATE_NAME => array(
                    'group' => Error::G_TEMPLATE,
                    'message' => "Invalid template name.",
                    'logger' => 'warning',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_CANNOT_WRITE_TEMPLATE => array(
                    'group' => Error::G_TEMPLATE,
                    'message' => "Unable to write template.",
                    'logger' => 'error',
                    'public' => false,
                    'debug' => true,
            ),
            Error::E_ENGINE_NOT_FOUND => array(
                    'group' => Error::G_ENGINE,
                    'message' => "The Twig version you requested is not implemented.",
                    'logger' => 'warning',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_EXECUTION_FAILURE => array(
                    'group' => Error::G_EXECUTION,
                    'message' => "The fiddle execution failed.",
                    'logger' => 'info',
                    'public' => true,
                    'debug' => false,
            ),
            Error::E_UNKNOWN_COMPILED_FILE => array(
                    'group' => Error::G_EXECUTION,
                    'message' => "Can't get template name from a compiled file.",
                    'logger' => 'error',
                    'public' => true,
                    'debug' => true,
            ),
            Error::E_UNEXPECTED_COMPILED_FILE => array(
                    'group' => Error::G_EXECUTION,
                    'message' => "Got an unexpected template name.",
                    'logger' => 'error',
                    'public' => true,
                    'debug' => true,
            ),
    );

    public function getError($no)
    {
        $trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2), 1);
        $caller = $trace[0]['file'] . ':' . $trace[0]['line'];

        if (!array_key_exists($no, $this->errors))
        {
            $this->logger->error("{$caller} given an unknown error.");
            $no = Error::E_UNKNOWN;
        }

        $details = $this->errors[$no];
        $context = array_slice(func_get_args(), 1);

        $this->logger->{$details['logger']}("Error requested by {$caller}:");
        $this->logger->{$details['logger']}($details['message'], $context);

        $error = new Error();
        $error->setNo($no);
        $error->setGroup($details['group']);
        $error->setMessage($details['message']);
        $error->setContext($context);
        $error->setIsPublic($details['public']);
        $error->setIsDebug($details['debug']);
        $error->setCaller($caller);

        return $error;
    }

}
