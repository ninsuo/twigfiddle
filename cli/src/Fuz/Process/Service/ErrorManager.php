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
use Fuz\Process\Entity\Error;

class ErrorManager extends BaseService
{

    protected $errors = array(
        Error::E_UNKNOWN                   => array(
            'group'   => Error::G_GENERAL,
            'message' => "An unknwon error occured.",
            'logger'  => 'error',
            'debug'   => true,
        ),
        Error::E_UNEXPECTED                => array(
            'group'   => Error::G_GENERAL,
            'message' => "An unexpected error occured.",
            'logger'  => 'error',
            'debug'   => true,
        ),
        Error::E_UNEXPECTED_NODEBUG        => array(
            'group'   => Error::G_GENERAL,
            'message' => "An unexpected error occured.",
            'logger'  => 'error',
            'debug'   => false,
        ),
        Error::E_TIMEOUT                   => array(
            'group'   => Error::G_GENERAL,
            'message' => "Maximum execution timeout was reached.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_INVALID_ENVIRONMENT_ID    => array(
            'group'   => Error::G_ENVIRONMENT,
            'message' => "The given environment ID does not match with the expected format.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_UNEXISTING_ENVIRONMENT_ID => array(
            'group'   => Error::G_ENVIRONMENT,
            'message' => "The given environment ID does not have an associated directory.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_UNEXISTING_SHARED_MEMORY  => array(
            'group'   => Error::G_ENVIRONMENT,
            'message' => "The envrionment's shared memory does not exist.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_UNREADABLE_SHARED_MEMORY  => array(
            'group'   => Error::G_ENVIRONMENT,
            'message' => "The envrionment's shared memory is not readable.",
            'logger'  => 'error',
            'debug'   => false,
        ),
        Error::E_FIDDLE_ALREADY_RUN        => array(
            'group'   => Error::G_ENVIRONMENT,
            'message' => "This fiddle has already been started.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_FIDDLE_NOT_STORED         => array(
            'group'   => Error::G_ENVIRONMENT,
            'message' => "Fiddle not found in the shared object.",
            'logger'  => 'error',
            'debug'   => false,
        ),
        Error::E_UNKNOWN_CONTEXT_FORMAT    => array(
            'group'   => Error::G_CONTEXT,
            'message' => "The given context format is not supported.",
            'logger'  => 'error',
            'debug'   => true,
        ),
        Error::E_INVALID_CONTEXT_SYNTAX    => array(
            'group'   => Error::G_CONTEXT,
            'message' => "Unable to convert the given twig context to an array.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_INVALID_CONTEXT_TYPE      => array(
            'group'   => Error::G_CONTEXT,
            'message' => "The Twig context should be convertible to an array.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_NO_TEMPLATE               => array(
            'group'   => Error::G_TEMPLATE,
            'message' => "This fiddle does not contain any template.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_NO_MAIN_TEMPLATE          => array(
            'group'   => Error::G_TEMPLATE,
            'message' => "This fiddle does not have a main template.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_SEVERAL_MAIN_TEMPLATES    => array(
            'group'   => Error::G_TEMPLATE,
            'message' => "This fiddle has several main templates.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_INVALID_TEMPLATE_NAME     => array(
            'group'   => Error::G_TEMPLATE,
            'message' => "Invalid template name.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_CANNOT_WRITE_TEMPLATE     => array(
            'group'   => Error::G_TEMPLATE,
            'message' => "Unable to write template.",
            'logger'  => 'error',
            'debug'   => true,
        ),
        Error::E_ENGINE_NOT_FOUND          => array(
            'group'   => Error::G_EXECUTION,
            'message' => "The Twig engine you requested is not implemented.",
            'logger'  => 'warning',
            'debug'   => false,
        ),
        Error::E_C_NOT_SUPPORTED           => array(
            'group'   => Error::G_EXECUTION,
            'message' => "The Twig version you requested does not have a C extension.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_C_UNABLE_TO_DL            => array(
            'group'   => Error::G_EXECUTION,
            'message' => "Can't load the C extension. Is enable_dl set to 'On' in php.ini?",
            'logger'  => 'error',
            'debug'   => true,
        ),
        Error::E_EXECUTION_FAILURE         => array(
            'group'   => Error::G_EXECUTION,
            'message' => "The fiddle execution failed.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_TWIG_LOADER_ERROR         => array(
            'group'   => Error::G_EXECUTION,
            'message' => "The Twig Loader thrown an exception.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_TWIG_SYNTAX_ERROR         => array(
            'group'   => Error::G_EXECUTION,
            'message' => "The given Twig code contains syntax error(s).",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_TWIG_RUNTIME_ERROR        => array(
            'group'   => Error::G_EXECUTION,
            'message' => "A Twig's runtime exception has been trown.",
            'logger'  => 'info',
            'debug'   => false,
        ),
        Error::E_UNKNOWN_COMPILED_FILE     => array(
            'group'   => Error::G_EXECUTION,
            'message' => "Can't get template name from a compiled file.",
            'logger'  => 'error',
            'debug'   => true,
        ),
        Error::E_UNEXPECTED_COMPILED_FILE  => array(
            'group'   => Error::G_EXECUTION,
            'message' => "Got an unexpected template name.",
            'logger'  => 'error',
            'debug'   => true,
        ),
    );

    public function getError($no, $context = array ())
    {
        $trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2), 1);
        $caller = $trace[0]['file'] . ':' . $trace[0]['line'];

        if (!array_key_exists($no, $this->errors))
        {
            $this->logger->error("{$caller} given an unknown error.");
            $no = Error::E_UNKNOWN;
        }

        if ((is_object($context) && ($context instanceof \Exception)))
        {
            $context = array (
                    'type' => get_class($context),
                    'message' => $context->getMessage(),
                    'at' => $context->getFile() . ':' . $context->getLine(),
            );
        }

        $details = $this->errors[$no];

        $this->logger->{$details['logger']}("Error requested by {$caller}:");
        $this->logger->{$details['logger']}($details['message'], $context);

        $error = new Error();
        $error->setNo($no);
        $error->setGroup($details['group']);
        $error->setMessage($details['message']);
        $error->setContext($context);
        $error->setDebug($details['debug']);
        $error->setCaller($caller);

        return $error;
    }

}
