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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ExportFiddleCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();
        $this
           ->setName('twigfiddle:export:fiddle')
           ->setDescription('Export a fiddle as a json string')
           ->addArgument('hash', InputArgument::REQUIRED, "Fiddle's hash")
           ->addArgument('revision', InputArgument::REQUIRED, "Fiddle's revision")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hash     = $input->getArgument('hash');
        $revision = $input->getArgument('revision');

        $fiddle = $this
           ->getContainer()
           ->get('doctrine')
           ->getRepository('FuzAppBundle:Fiddle')
           ->getFiddle($hash, $revision)
        ;

        if (is_null($fiddle->getId())) {
            $output->writeln("<error>Fiddle {$hash}:{$revision} does not exist.</error>");

            return 1;
        }

        $fiddleJson = $this->serialize($fiddle, array('id', 'user', 'context', 'templates', 'tags', 'creationTm', 'updateTm'));
        $contextJson = $this->serialize($fiddle->getContext(), array('fiddle'));
        $templatesJson = array();
        foreach ($fiddle->getTemplates() as $template) {
            $templatesJson[] = $this->serialize($template, array('fiddle'));
        }
        $tagsJson = array();
        foreach ($fiddle->getTags() as $tag) {
            $tagsJson[] = $this->serialize($tag, array('fiddle'));
        }

        $export = array($fiddleJson, $contextJson, $templatesJson, $tagsJson);

        $encoder = new JsonEncoder();
        $serializer = new Serializer(array(), array($encoder));
        $json       = $serializer->encode($export, 'json');

        $output->writeln($json);

        return 0;
    }

    protected function serialize($object, $ignoredAttributes)
    {
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes($ignoredAttributes);
        $encoder    = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $json       = $serializer->serialize($object, 'json');
        return $json;
    }

}
