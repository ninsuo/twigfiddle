<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\FiddleContext;
use Fuz\AppBundle\Entity\FiddleTemplate;
use Fuz\AppBundle\Entity\FiddleTag;

class ImportCommand extends ContainerAwareCommand
{

    protected $output;
    protected $error = false;
    protected $file;

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("twigfiddle:import")
           ->setDescription("Import a fiddle")
           ->addArgument('files', InputArgument::IS_ARRAY, "Fiddle's JSON files")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');
        $this->output = $output;

        $em = $this
           ->getContainer()
           ->get('doctrine.orm.entity_manager')
        ;

        $fiddleRepo = $this
           ->getContainer()
           ->get('doctrine')
           ->getRepository('FuzAppBundle:Fiddle')
        ;

        $em = $this
           ->getContainer()
           ->get('doctrine.orm.entity_manager')
        ;

        foreach ($files as $file)
        {
            $this->error = false;
            $this->file = $file;

            $string = @file_get_contents($file);
            if ($string === false)
            {
                $output->writeln("<error>File {$file} does not exist or is not readable.</error>");
                continue;
            }

            $json = @json_decode($string, true);
            if ($json === false)
            {
                $output->writeln("<error>File {$file} does not contain a valid json string.</error>");
                continue;
            }

            $fiddle = new Fiddle();
            $fiddle->setHash($this->getFromArray($json, 'hash'));
            $fiddle->setRevision($this->getFromArray($json, 'revision'));
            $fiddle->setTwigVersion($this->getFromArray($json, 'twig-version'));

            $context = new FiddleContext();
            $context->setFormat($this->getFromArray($json, 'context', 'format'));
            $context->setContent($this->getFromArray($json, 'context', 'content'));

            if (!is_null($context->getFormat()))
            {
                $fiddle->setContext($context);
            }

            $fiddle->clearTemplates();
            $jsonTemplates = $this->getFromArray($json, 'templates') ? : array ();
            foreach ($jsonTemplates as $jsonTemplate)
            {
                $template = new FiddleTemplate();
                $template->setFilename($this->getFromArray($jsonTemplate, 'filename'));
                $template->setContent($this->getFromArray($jsonTemplate, 'content'));
                $template->setIsMain($this->getFromArray($jsonTemplate, 'is-main'));
                $fiddle->addTemplate($template);
            }

            $fiddle->setTitle($this->getFromArray($json, 'title'));

            $tags = new ArrayCollection();
            $jsonTags = $this->getFromArray($json, 'tags');
            foreach ($jsonTags as $jsonTag)
            {
                $tag = new FiddleTag();
                $tag->setTag($jsonTag);
                $tags->add($tag);
            }
            $fiddle->setTags($tags);

            if ($this->error)
            {
                continue;
            }

            $check = $fiddleRepo->getFiddle($fiddle->getHash(), $fiddle->getRevision());

            if (!is_null($check->getId()))
            {
                $em->remove($check);
                $em->flush();
            }

            $em->persist($fiddle);
            $em->flush();

            $id = $fiddle->getId();
            $output->writeln("Imported: {$file} as fiddle ID = {$id}");
        }
    }

    protected function getFromArray(array $array)
    {
        $ref = &$array;
        $keys = array_slice(func_get_args(), 1);
        $keys_string = implode(' > ', $keys);
        $keys_count = count($keys);
        foreach ($keys as $index => $key)
        {
            if (!array_key_exists($key, $ref))
            {
                $this->output->writeln("<error>JSON contained in {$this->file} does not contain a value at {$keys_string}.</error>");
                $this->error = true;
                return null;
            }

            if (($index + 1) == $keys_count)
            {
                return $ref[$key];
            }

            $ref = &$ref[$key];
        }
    }

}
