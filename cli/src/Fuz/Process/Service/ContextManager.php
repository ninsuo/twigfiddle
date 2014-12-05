<?php

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Helper\ContextHelper;

class ContextManager extends BaseService
{

    public function __construct(ContextHelper $contextHelper)
    {
        $this->helper = $contextHelper;
    }

    public function convertFileToArray()
    {

    }

}
