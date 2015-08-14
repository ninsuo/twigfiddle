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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ImportCommand extends ContainerAwareCommand
{

    protected $output;

    protected function configure()
    {
        parent::configure();
        $this
           ->setName('twigfiddle:import')
           ->setDescription('Import a fiddle')
           ->addArgument('files', InputArgument::IS_ARRAY, "Fiddle's JSON files")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files        = $input->getArgument('files');
        $this->output = $output;

        $em = $this
           ->getContainer()
           ->get('doctrine')
           ->getManager()
        ;

        $fiddleRepo = $this
           ->getContainer()
           ->get('doctrine')
           ->getRepository('FuzAppBundle:Fiddle')
        ;

        foreach ($files as $file) {

            $json = file_get_contents($file);
            if ($json === false) {
                $output->writeln("<error>File {$file} does not exist or is not readable.</error>");
                continue;
            }

            $normalizer = new ObjectNormalizer();
            $normalizer->setCallbacks(array(
                'context' => function($attributeValue) {
                    // transform attributeValue (array) to FiddleContext
                },
                'templates' => function($attributeValue) {
                    // transform attributeValue (array) to an ArrayCollection of FiddleTemplate
                },
                'tags' => function($attributeValue) {
                    // transform attributeValue (array) to an ArrayCollection of FiddleTag
                },
            ));

            $newFiddle = $this->deserialize($json, 'Fuz\AppBundle\Entity\Fiddle', $normalizer);

            $fiddle = $fiddleRepo->getFiddle($newFiddle->getHash(), $newFiddle->getRevision());
            foreach ($fiddle->getTemplates() as $template) {
                if ($fiddle->getId()) {
                    $em->remove($template);
                    $em->flush($template);
                }
            }
            $fiddle->clearTemplates();
            foreach ($fiddle->getTags() as $tag) {
                if ($fiddle->getId()) {
                    $em->remove($tag);
                    $em->flush($tag);
                }
            }
            $fiddle->setTags(new ArrayCollection());

            // todo
            // - decode fiddle
            // - persist all sub-entities
            // - persist fiddle
//
//
//            $json = json_decode($string, true);
//            if ($json === false) {
//                $output->writeln("<error>File {$file} does not contain a valid json string.</error>");
//                continue;
//            }
//
//            $hash     = $this->getFromArray($json, 'hash');
//            $revision = $this->getFromArray($json, 'revision');
//
//            $fiddle = $fiddleRepo->getFiddle($hash, $revision);
//            $fiddle->setHash($hash);
//            $fiddle->setRevision($revision);
//            $fiddle->setTwigVersion($this->getFromArray($json, 'twig-version'));
//            $fiddle->getContext()->setFormat($this->getFromArray($json, 'context', 'format'));
//            $fiddle->getContext()->setContent($this->getFromArray($json, 'context', 'content'));
//
//            $jsonTemplates = $this->getFromArray($json, 'templates') ? : array();
//            foreach ($jsonTemplates as $jsonTemplate) {
//                $template = new FiddleTemplate();
//                $template->setFilename($this->getFromArray($jsonTemplate, 'filename'));
//                $template->setContent($this->getFromArray($jsonTemplate, 'content'));
//                $template->setMain($this->getFromArray($jsonTemplate, 'is-main'));
//                $fiddle->addTemplate($template);
//            }
//
//            $fiddle->setTitle($this->getFromArray($json, 'title'));
//
//
//
//            $tags     = new ArrayCollection();
//            $jsonTags = $this->getFromArray($json, 'tags');
//            foreach ($jsonTags as $jsonTag) {
//                $tag = new FiddleTag();
//                $tag->setTag($jsonTag);
//                $tags->add($tag);
//            }
//            $fiddle->setTags($tags);
//
//            if ($this->error) {
//                $em->detach($fiddle);
//                continue;
//            }

            $em->persist($fiddle);
            $em->flush();

            $id = $fiddle->getId();
            $output->writeln("Imported: {$file} as fiddle ID = {$id}");
        }
    }

    protected function deserialize($object, $type, $normalizer)
    {
        $encoder    = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $fiddle     = $serializer->deserialize($object, $type, 'json');
        return $fiddle;
    }

}
