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
                    'public' => false,
                    'debug' => true,
            ),
            Error::E_UNEXPECTED => array (
                    'group' => Error::G_GENERAL,
                    'message' => "An unexpected error occured.",
                    'public' => false,
                    'debug' => true,
            ),
            Error::E_INVALID_ENVIRONMENT_ID => array (
                    'group' => Error::G_ENVIRONMENT,
                    'message' => "The given environment ID is invalid (allowed: alphanumeric chars and hyphen).",
                    'public' => false,
                    'debug' => false,
            ),
    );

    public function getError($errno)
    {
        $trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2), 1);
        $caller = $trace[0]['file'].':'.$trace[0]['line'];

        if (!array_key_exists($errno, $this->errors))
        {
            $this->logger->error("{$caller} given an unknown error.");
            $errno = Error::E_UNKNOWN;
        }

        $details = $this->errors[$errno];

        $this->logger->info("Error requested by {$caller}: {$details['message']}");

        $error = new Error();
        $error->setErrno($errno);
        $error->setGroup($details['group']);
        $error->setErrstr($details['message']);
        $error->setIsPublic($details['public']);
        $error->setIsDebug($details['debug']);
        $error->setCaller($caller);

        return $error;
    }

}
