<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\Command;

use Fuz\Framework\Base\BaseCommand;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ReleaseWatcherCommand extends BaseCommand
{
    protected $cliDir;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->cliDir = __DIR__.'/../../../../../cli/';
    }

    protected function configure()
    {
        parent::configure();

        $this
           ->setName('twigfiddle:release:watcher')
           ->setDescription('Fetch, configure and install new Twig and Twig-extension releases')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->regenerateConfigurationAndTests();

        return 0;

        $this
           ->fetchNewTwigReleases()
           ->fetchNewTwigExtensionsReleses()
        ;
    }

    protected function fetchNewTwigExtensionsReleses()
    {
        $response = (new Client())->get('https://api.github.com/repos/twigphp/twig-extensions/tags', [
            'headers' => [
                'Accept' => 'application/vnd.github.mercy-preview+json',
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        foreach ($result as $tag) {
            $version = 'Twig-extensions-'.substr($tag['name'], 1);

            $gzPath = $this->cliDir."/twig/extension/compressed/{$version}.tar.gz";
            if (is_file($gzPath)) {
                continue;
            }

            $this->downloadAndPrepare($tag['tarball_url'], $version, true);
        }

        return $this;
    }

    protected function fetchNewTwigReleases()
    {
        $newReleasesCounter = 0;

        $response = (new Client())->get('https://api.github.com/repos/twigphp/twig/tags', [
            'headers' => [
                'Accept' => 'application/vnd.github.mercy-preview+json',
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true);
        foreach ($result as $tag) {
            $version = 'Twig-'.substr($tag['name'], 1);

            $gzPath = $this->cliDir."/twig/compressed/{$version}.tar.gz";
            if (is_file($gzPath)) {
                continue;
            }

            $this->downloadAndPrepare($tag['tarball_url'], $version, false);

            ++$newReleasesCounter;
        }

        if ($newReleasesCounter) {
            $this->regenerateConfigurationAndTests();
        }

        return $this;
    }

    protected function downloadAndPrepare($url, $version, $isExtension)
    {
        // --------------------------------------
        // downloading release
        // --------------------------------------

        $gz      = (new Client())->get($url)->getBody()->getContents();
        $tmpDir  = '/tmp/'.Uuid::uuid4();
        mkdir($tmpDir);
        file_put_contents("{$tmpDir}/release.tgz", $gz);

        // --------------------------------------
        // renaming random directory name to Twig-x.y.z
        // --------------------------------------

        exec("( cd $tmpDir && tar xzf release.tgz )");
        $twigDir = basename(glob("{$tmpDir}/*Twig-*")[0]);
        rename("{$tmpDir}/{$twigDir}", "{$tmpDir}/{$version}");
        exec("( cd $tmpDir && tar czf {$version}.tar.gz {$version} )");

        // --------------------------------------
        // moving files to the right places
        // --------------------------------------

        if ($isExtension) {
            rename("{$tmpDir}/{$version}.tar.gz", $this->cliDir."/twig/extension/compressed/{$version}.tar.gz");
            rename("{$tmpDir}/{$version}", $this->cliDir."/twig/extension/uncompressed/{$version}");
        } else {
            rename("{$tmpDir}/{$version}.tar.gz", $this->cliDir."/twig/compressed/{$version}.tar.gz");
            rename("{$tmpDir}/{$version}", $this->cliDir."/twig/uncompressed/{$version}");
        }

        exec("rm -rf $tmpDir"); // hopefully, Uuid::uuid4 won't return "../" :)
    }

    protected function regenerateConfigurationAndTests()
    {
        // --------------------------------------
        // getting and sorting all available versions
        // --------------------------------------

        $versions  = [];
        $twigPaths = glob($this->cliDir.'/twig/compressed/Twig-*.tar.gz');
        foreach ($twigPaths as $twigPath) {
            $versions[] = substr(basename($twigPath), 0, strpos(basename($twigPath), '.tar.gz'));
        }

        usort($versions, 'version_compare');
        $versionsReversed = array_reverse($versions);

        $dividedInMajorVersions = [];

        foreach ($versionsReversed as $version) {
            $majorVersion = intval(substr($version, 5, 1));

            if (!array_key_exists($majorVersion, $dividedInMajorVersions)) {
                $dividedInMajorVersions[$majorVersion] = [];
            }

            $dividedInMajorVersions[$majorVersion][] = $version;
        }

        // --------------------------------------
        // regenerating twig engines configuration
        // --------------------------------------

        $config = $this->cliDir.'/config/services/twig_engines.yml';
        $yml    = Yaml::parse($config);

        foreach ($dividedInMajorVersions as $majorVersion => $versions) {
            $key = sprintf('v%s.twig_engine', $majorVersion);

            if (!isset($yml['services'][$key])) {
                $yml['services'][$key] = [
                    'class' => '%twig_engine.class%',
                    'tags' => [
                        [
                            'name' => 'twig.engine',
                            'versions' => [],
                            'label' => sprintf('Twig %s.x', $majorVersion),
                        ],
                    ],
                ];
            }

            $yml['services'][$key]['tags'][0]['versions'] = implode(' / ', $dividedInMajorVersions[$majorVersion]);
        }
        krsort($yml['services']);

        file_put_contents($config, Yaml::dump($yml, 5));

        // --------------------------------------
        // regenerating tests
        // --------------------------------------

        $test    = $this->cliDir.'//test/integration/DefaultEngineTest.php';
        $content = file_get_contents($test);

        $headerDelimiter = '// --- header for auto-generation ---';
        $footerDelimiter = '// --- footer for auto-generation ---';

        $header = substr($content, 0, strpos($content, $headerDelimiter) + strlen($headerDelimiter));
        $footer = substr($content, strpos($content, $footerDelimiter));

        $generated = "\n\n";
        foreach (array_reverse($dividedInMajorVersions, true) as $majorVersion => $versions) {
            $generated .= "            // {$majorVersion}.x\n";
            foreach ($versions as $version) {
                $generated .= "            array('Twig {$majorVersion}.x', '$version'),\n";
            }
            $generated .= "\n";
        }
        $generated .= '            ';

        file_put_contents($test, $header.$generated.$footer);
    }
}
