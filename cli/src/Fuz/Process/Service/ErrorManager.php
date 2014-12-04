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
            Error::E_INVALID_ENVIRONMENT_ID => array (
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "The given environment ID is invalid (allowed: alphanumeric chars and hyphen).",
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
        $error->setIsPublic($details['public']);
        $error->setIsDebug($details['debug']);
        $error->setCaller($caller);

        return $error;
    }

}
