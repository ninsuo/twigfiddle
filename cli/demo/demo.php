<?php

namespace Fuz\AppBundle\Entity;

if (!include __DIR__ . '/../vendor/autoload.php')
{
    die('You must set up the project dependencies.');
}

use Doctrine\Common\Collections\ArrayCollection;
use Fuz\Component\SharedMemory\Storage\StorageFile;
use Fuz\Component\SharedMemory\SharedMemory;

$fiddle = new Fiddle();

$config = new FiddleConfig();
$config->setTwigVersion("Twig-1.16.2");
$fiddle->setConfig($config);

$templates = new ArrayCollection();
$fiddle->setTemplates($templates);

$templateA = new FiddleTemplate();
$templateA->setIsMain(true);
$templateA->setFilename("main.twig");
$templateA->setContent(file_get_contents(__DIR__."/main.twig"));
$templates->add($templateA);

$templateB = new FiddleTemplate();
$templateB->setIsMain(false);
$templateB->setFilename("macros.twig");
$templateB->setContent(file_get_contents(__DIR__."/macros.twig"));
$templates->add($templateB);

$templateC = new FiddleTemplate();
$templateC->setIsMain(false);
$templateC->setFilename("blocks.twig");
$templateC->setContent(file_get_contents(__DIR__."/blocks.twig"));
$templates->add($templateC);

//$context = new FiddleContext();
//$context->setFormat(FiddleContext::FORMAT_YAML);
//$context->setContent(file_get_contents(__DIR__."/context.yml"));
//$fiddle->setContext($context);

//$context = new FiddleContext();
//$context->setFormat(FiddleContext::FORMAT_XML);
//$context->setContent(file_get_contents(__DIR__."/context.xml"));
//$fiddle->setContext($context);

$context = new FiddleContext();
$context->setFormat(FiddleContext::FORMAT_JSON);
$context->setContent(file_get_contents(__DIR__."/context.json"));
$fiddle->setContext($context);


$dir = __DIR__ . '/../../environment/demo/';
if (!is_dir($dir))
{
    mkdir($dir, 0755);
}

$storage = new StorageFile("{$dir}fiddle.shr");
$shared = new SharedMemory($storage);

$shared->fiddle = $fiddle;
$shared->begin_tm = null;
$shared->finish_tm = null;
$shared->errors = array();
