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
$templateA->setContent(file_get_contents('main.twig'));
$templates->add($templateA);

$templateB = new FiddleTemplate();
$templateB->setIsMain(false);
$templateB->setFilename("macros.twig");
$templateB->setContent(file_get_contents('macros.twig'));
$templates->add($templateB);

$templateC = new FiddleTemplate();
$templateC->setIsMain(false);
$templateC->setFilename("blocks.twig");
$templateC->setContent(file_get_contents('blocks.twig'));
$templates->add($templateC);

$context = new FiddleContext();
$context->setFormat(FiddleContext::FORMAT_YAML);
$context->setContent(file_get_contents('context.yml'));
$fiddle->setContext($context);

$storage = new StorageFile(__DIR__ . '/../../environment/demo.fiddle');
$shared = new SharedMemory($storage);
$shared->fiddle = $fiddle;
