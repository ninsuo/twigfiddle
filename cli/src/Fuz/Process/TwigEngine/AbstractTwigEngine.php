<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\TwigEngine;

use Fuz\Framework\Base\BaseService;
use Fuz\Process\Agent\FiddleAgent;

abstract class AbstractTwigEngine extends BaseService implements TwigEngineInterface
{
    /**
     * @var FiddleAgent
     */
    protected $agent;

    public function __construct(FiddleAgent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * The first coomment of all compiled twig file contains the twig file name since the very first Twig's version.
     * This method just extracts it.
     *
     * @param string $cacheDirectory
     * @param array  $files
     *
     * @return array
     */
    public function extractTemplateName($content)
    {
        $templateName = null;
        $tokens = token_get_all($content);
        foreach ($tokens as $token) {
            if (!is_array($token)) {
                continue;
            }
            list($identifier, $string) = $token;
            if ($identifier !== T_COMMENT) {
                continue;
            }
            $templateName = trim(str_replace(array('/*', '*/'), '', $string));
            break;
        }

        return $templateName;
    }
}
