<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!include __DIR__ . '/../../vendor/autoload.php')
{
    die('You must set up the project dependencies.');
}

use Symfony\Component\Filesystem\Filesystem;
use Fuz\Component\SharedMemory\Storage\StorageFile;
use Fuz\Component\SharedMemory\SharedMemory;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\FiddleContext;
use Fuz\AppBundle\Entity\FiddleTemplate;

class DefaultEngineTest extends \PHPUnit_Framework_TestCase
{

    const PHP_PATH = '/usr/bin/php';

    protected $fs;
    protected $envId;
    protected $envDir;
    protected $rootDir;
    protected $shared;

    public function __construct($name = null, array $data = array (), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fs = new Filesystem();
        $this->envId = 'test';
        $this->envDir = __DIR__ . '/../environment';
        $this->rootDir = __DIR__ . '/../..';
    }

    public function setUp()
    {
        $this->fs->mkdir($this->envDir);
        $this->fs->mkdir("{$this->envDir}/{$this->envId}");
    }

    public function tearDown()
    {
        $this->fs->remove($this->envDir);
    }

    /**
     * Test Twigfiddle's processor with all supported versions.
     *
     * @dataProvider versionProvider
     */
    public function testVersion($version)
    {
        $this
           ->prepareFiddle($version)
           ->runFiddle()
           ->checkFiddle()
        ;
    }

    public function versionProvider()
    {
        return array (
                // 2.x
                array ('Twig-master'),

                // 1.x
                array ('Twig-1.20.0'),
                array ('Twig-1.19.0'),
                array ('Twig-1.18.2'),
                array ('Twig-1.18.1'),
                array ('Twig-1.18.0'),
                array ('Twig-1.17.0'),
                array ('Twig-1.16.3'),
                array ('Twig-1.16.2'),
                array ('Twig-1.16.1'),
                array ('Twig-1.16.0'),
                array ('Twig-1.15.1'),
                array ('Twig-1.15.0'),
                array ('Twig-1.14.2'),
                array ('Twig-1.14.1'),
                array ('Twig-1.14.0'),
                array ('Twig-1.13.2'),
                array ('Twig-1.13.1'),
                array ('Twig-1.13.0'),
                array ('Twig-1.12.3'),
                array ('Twig-1.12.2'),
                array ('Twig-1.12.1'),
                array ('Twig-1.12.0-RC1'),
                array ('Twig-1.12.0'),
                array ('Twig-1.11.1'),
                array ('Twig-1.11.0'),
                array ('Twig-1.10.3'),
                array ('Twig-1.10.2'),
                array ('Twig-1.10.1'),
                array ('Twig-1.10.0'),
                array ('Twig-1.9.2'),
                array ('Twig-1.9.1'),
                array ('Twig-1.9.0'),
                array ('Twig-1.8.3'),
                array ('Twig-1.8.2'),
                array ('Twig-1.8.1'),
                array ('Twig-1.8.0'),
                array ('Twig-1.7.0'),
                array ('Twig-1.6.5'),
                array ('Twig-1.6.4'),
                array ('Twig-1.6.3'),
                array ('Twig-1.6.2'),
                array ('Twig-1.6.1'),
                array ('Twig-1.6.0'),
                array ('Twig-1.5.1'),
                array ('Twig-1.5.0-RC2'),
                array ('Twig-1.5.0-RC1'),
                array ('Twig-1.5.0'),
                array ('Twig-1.4.0-RC2'),
                array ('Twig-1.4.0-RC1'),
                array ('Twig-1.4.0'),
                array ('Twig-1.3.0-RC1'),
                array ('Twig-1.3.0'),
                array ('Twig-1.2.0-RC1'),
                array ('Twig-1.2.0'),
                array ('Twig-1.1.2'),
                array ('Twig-1.1.1'),
                array ('Twig-1.1.0-RC3'),
                array ('Twig-1.1.0-RC2'),
                array ('Twig-1.1.0-RC1'),
                array ('Twig-1.1.0'),
                array ('Twig-1.0.0-RC2'),
                array ('Twig-1.0.0-RC1'),
                array ('Twig-1.0.0'),
                array ('Twig-0.9.10'),
                array ('Twig-0.9.9'),
                array ('Twig-0.9.8'),
                array ('Twig-0.9.7'),
                array ('Twig-0.9.6'),
                array ('Twig-0.9.5'),
                array ('Twig-0.9.4'),
                array ('Twig-0.9.2'),
                array ('Twig-0.9.1'),
                array ('Twig-0.9.0'),
        );
    }

    protected function prepareFiddle($version)
    {
        $fiddle = new Fiddle();
        $fiddle->setTwigVersion($version);

        $context = new FiddleContext();
        $context->setFormat(FiddleContext::FORMAT_JSON);
        $context->setContent(json_encode(array ('name' => 'world')));
        $fiddle->setContext($context);

        $template = new FiddleTemplate();
        $template->setMain(true);
        $template->setFilename("template.twig");
        $template->setContent('Hello, {{ name }}!');

        $fiddle->clearTemplates();
        $fiddle->addTemplate($template);

        $storage = new StorageFile("{$this->envDir}/{$this->envId}/fiddle.shr");
        $this->shared = new SharedMemory($storage);

        $this->shared->fiddle = $fiddle;
        $this->shared->begin_tm = null;
        $this->shared->finish_tm = null;
        $this->shared->result = null;

        return $this;
    }

    protected function runFiddle()
    {
        $oldDir = getcwd();
        chdir(__DIR__);

        $command = self::PHP_PATH . ' ' . implode(' ',
              array_map('escapeshellarg',
                 array (
                   $this->rootDir . '/run-test.php',
                   'twigfiddle:run',
                   $this->envId,
        )));

        $out = exec("{$command} 2>&1");
        $this->assertEmpty($out);

        chdir($oldDir);
        return $this;
    }

    protected function checkFiddle()
    {
        $this->assertGreaterThan(0, $this->shared->begin_tm);
        $this->assertGreaterThan(0, $this->shared->finish_tm);
        $this->assertInstanceOf('\Fuz\Process\Entity\Result', $this->shared->result);

        $result = $this->shared->result;
        $this->assertEquals("Hello, world!", $result->getRendered());

        $compiled = $result->getCompiled();
        $this->assertEquals(1, count($compiled));
        $this->assertEquals('template.twig', key($compiled));

        // Interesting to see how evolved generated templates
//        echo $this->shared->fiddle->getTwigVersion(), PHP_EOL;
//        echo reset($compiled), PHP_EOL;

        return $this;
    }

}
