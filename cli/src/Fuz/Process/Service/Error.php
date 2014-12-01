<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Entity\Error;

class Error extends BaseService
{

    protected $errors = array (
            Error::E_UNKNOWN => array (
                    'group' => Error::GROUP_GENERAL,
                    'message' => "An unknwon error occured.",
                    'public' => false,
            ),
            Error::E_UNEXPECTED => array (
                    'group' => Error::GROUP_GENERAL,
                    'message' => "An unexpected error occured.",
                    'public' => false,
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

        $this->logger->debug("Error requested by {$caller}: {$details['message']}");

        $error = new Error();
        $error->setErrno($errno);
        $error->setGroup($details['group']);
        $error->setErrstr($details['message']);
        $error->setIsPublic($details['public']);
        $error->setCaller($caller);

        return $error;
    }

}
