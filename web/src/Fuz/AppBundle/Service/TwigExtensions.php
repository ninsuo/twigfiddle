<?php
/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Service;

use Doctrine\Common\Cache\ApcCache;
use Fuz\AppBundle\Util\ProcessConfiguration;
use Psr\Log\LoggerInterface;

class TwigExtensions
{
    protected $logger;
    protected $remoteConfig;
    protected $environment;

    public function __construct(LoggerInterface $logger, ProcessConfiguration $processConfiguration, $environment)
    {
        $this->logger = $logger;
        $this->remoteConfig = $processConfiguration->getProcessConfig();
        $this->environment = $environment;
    }

    public function getAvailableTwigExtensions()
    {
        if ($this->environment === 'prod') {
            $apc = new ApcCache();
            $id = $this->remoteConfig['twig_extensions']['apc_cache_key'];
            if ($apc->contains($id)) {
                return $apc->fetch($id);
            } else {
                $versions = $this->fetchAvailableTwigExtensions();
                $apc->save($id, $versions);

                return $versions;
            }
        }

        return $this->fetchAvailableTwigExtensions();
    }

    protected function fetchAvailableTwigExtensions()
    {
        $available = array();
        $dir = $this->remoteConfig['twig_extensions']['directory'];
        foreach (glob("{$dir}/*") as $extension) {
            $available[] = str_replace('Twig-extensions-', '', basename($extension));
        }

        usort($available, 'version_compare');

        return array_reverse($available);
    }
}
