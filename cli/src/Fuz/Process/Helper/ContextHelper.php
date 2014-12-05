<?php

namespace Fuz\Process\Helper;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Service\ErrorManager;
use Fuz\Process\Entity\Context;

class ContextHelper extends BaseService
{

    protected $errorManager;
    protected $context;

    public function __construct(ErrorManager $errorManager)
    {
        $this->errorManager = $errorManager;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function addError($no, array $details = array ())
    {
        $this->context->addError($this->errorManager->getError($no, $details));
    }

}
