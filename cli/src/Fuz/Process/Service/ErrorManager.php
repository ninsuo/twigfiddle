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
    protected $errors = [
        Error::E_UNKNOWN                   => [
            'group'   => Error::G_GENERAL,
            'message' => 'An unknwon error occured.',
            'logger'  => 'error',
            'debug'   => true,
        ],
        Error::E_DEBUG                     => [
            'group'   => Error::G_GENERAL,
            'message' => 'Debug Mode.',
            'logger'  => 'debug',
            'debug'   => false,
        ],
        Error::E_UNEXPECTED                => [
            'group'   => Error::G_GENERAL,
            'message' => 'An unexpected error occured.',
            'logger'  => 'error',
            'debug'   => true,
        ],
        Error::E_UNEXPECTED_NODEBUG        => [
            'group'   => Error::G_GENERAL,
            'message' => 'An unexpected error occured.',
            'logger'  => 'error',
            'debug'   => false,
        ],
        Error::E_TIMEOUT                   => [
            'group'   => Error::G_GENERAL,
            'message' => 'Maximum execution timeout was reached.',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_INVALID_ENVIRONMENT_ID    => [
            'group'   => Error::G_ENVIRONMENT,
            'message' => 'The given environment ID does not match with the expected format.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_UNEXISTING_ENVIRONMENT_ID => [
            'group'   => Error::G_ENVIRONMENT,
            'message' => 'The given environment ID does not have an associated directory.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_UNEXISTING_SHARED_MEMORY  => [
            'group'   => Error::G_ENVIRONMENT,
            'message' => "The envrionment shared memory does not exist.",
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_UNREADABLE_SHARED_MEMORY  => [
            'group'   => Error::G_ENVIRONMENT,
            'message' => "The envrionment shared memory is not readable.",
            'logger'  => 'error',
            'debug'   => false,
        ],
        Error::E_FIDDLE_ALREADY_RUN        => [
            'group'   => Error::G_ENVIRONMENT,
            'message' => 'This fiddle has already been started.',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_FIDDLE_NOT_STORED         => [
            'group'   => Error::G_ENVIRONMENT,
            'message' => 'Fiddle not found in the shared object.',
            'logger'  => 'error',
            'debug'   => false,
        ],
        Error::E_UNKNOWN_CONTEXT_FORMAT    => [
            'group'   => Error::G_CONTEXT,
            'message' => 'The given context format is not supported.',
            'logger'  => 'error',
            'debug'   => true,
        ],
        Error::E_INVALID_CONTEXT_SYNTAX    => [
            'group'   => Error::G_CONTEXT,
            'message' => 'Unable to convert the given twig context to an array.',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_INVALID_CONTEXT_TYPE      => [
            'group'   => Error::G_CONTEXT,
            'message' => 'The Twig context should be convertible to an array.',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_NO_TEMPLATE               => [
            'group'   => Error::G_TEMPLATE,
            'message' => 'This fiddle does not contain any template.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_NO_MAIN_TEMPLATE          => [
            'group'   => Error::G_TEMPLATE,
            'message' => 'This fiddle does not have a main template.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_SEVERAL_MAIN_TEMPLATES    => [
            'group'   => Error::G_TEMPLATE,
            'message' => 'This fiddle has several main templates.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_INVALID_TEMPLATE_NAME     => [
            'group'   => Error::G_TEMPLATE,
            'message' => 'Invalid template name.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_CANNOT_WRITE_TEMPLATE     => [
            'group'   => Error::G_TEMPLATE,
            'message' => 'Unable to write template.',
            'logger'  => 'error',
            'debug'   => true,
        ],
        Error::E_ENGINE_NOT_FOUND          => [
            'group'   => Error::G_EXECUTION,
            'message' => 'The Twig engine you requested is not implemented.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
        Error::E_EXECUTION_FAILURE         => [
            'group'   => Error::G_EXECUTION,
            'message' => 'The fiddle execution failed.',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_TWIG_LOADER_ERROR         => [
            'group'   => Error::G_EXECUTION,
            'message' => 'The Twig Loader thrown an exception.',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_TWIG_SYNTAX_ERROR         => [
            'group'   => Error::G_EXECUTION,
            'message' => 'The given Twig code contains syntax error(s).',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_TWIG_RUNTIME_ERROR        => [
            'group'   => Error::G_EXECUTION,
            'message' => "A Twig runtime exception has been thrown.",
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_UNKNOWN_COMPILED_FILE     => [
            'group'   => Error::G_EXECUTION,
            'message' => "Can't get template name from a compiled file.",
            'logger'  => 'error',
            'debug'   => true,
        ],
        Error::E_UNEXPECTED_COMPILED_FILE  => [
            'group'   => Error::G_EXECUTION,
            'message' => 'Got an unexpected template name.',
            'logger'  => 'error',
            'debug'   => true,
        ],
        Error::E_C_NOT_SUPPORTED           => [
            'group'   => Error::G_OPTION,
            'message' => 'The Twig version you requested does not have a C extension.',
            'logger'  => 'info',
            'debug'   => false,
        ],
        Error::E_C_UNABLE_TO_DL            => [
            'group'   => Error::G_OPTION,
            'message' => 'The C extension exists but has not been loaded successfully.',
            'logger'  => 'error',
            'debug'   => true,
        ],
        Error::E_UNKNOWN_TWIG_EXTENSION    => [
            'group'   => Error::G_OPTION,
            'message' => 'The requested Twig extension version is not supported.',
            'logger'  => 'warning',
            'debug'   => false,
        ],
    ];

    public function getError($no, $context = [])
    {
        $trace  = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2), 1);
        $caller = $trace[0]['file'].':'.$trace[0]['line'];

        if (!array_key_exists($no, $this->errors)) {
            $this->logger->error("{$caller} given an unknown error.");
            $no = Error::E_UNKNOWN;
        }

        if ((is_object($context) && ($context instanceof \Exception))) {
            $context = [
                'type'    => get_class($context),
                'message' => $context->getMessage(),
                'at'      => $context->getFile().':'.$context->getLine(),
            ];
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
