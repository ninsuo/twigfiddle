<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Twig\Extension;

class GitHubExtension extends \Twig_Extension
{

    protected $repositoryRoot;

    public function __construct($repositoryRoot)
    {
        $this->repositoryRoot = $repositoryRoot;
    }

    /**
     * Replaces:
     * twigfiddle:cli/src/Fuz/Process/Service/ExecuteManager.php:98
     *
     * By:
     * https://github.com/ninsuo/twigfiddle/blob/master/cli/src/Fuz/Process/Service/ExecuteManager.php#L98
     *
     * @return array[\Twig_SimpleFilter]
     */
    public function getFilters()
    {
        return array (
                new \Twig_SimpleFilter('github_repository_link',
                   function($relativePath)
                   {
                        // cli/src/Fuz/Process/Service/ExecuteManager.php:98
                       $path = substr($relativePath, strpos($relativePath, ':') + 1);

                        // #L98
                       $line = '#L' . substr($path, strrpos($path, ':') + 1);

                        // cli/src/Fuz/Process/Service/ExecuteManager.php
                       $path = substr($path, 0, strrpos($path, ':'));

                       // https://github.com/ninsuo/twigfiddle/blob/master/cli/src/Fuz/Process/Service/ExecuteManager.php#L98
                       return $this->repositoryRoot . '/blob/master/' . $path . $line;
                   }),
        );
    }

    public function getName()
    {
        return 'FuzAppBundle:GitHub';
    }

}
