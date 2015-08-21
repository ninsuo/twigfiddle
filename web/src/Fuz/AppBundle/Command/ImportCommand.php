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

            $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
            $newFiddle  = $serializer->deserialize($json, 'Fuz\AppBundle\Entity\Fiddle', 'json');
            $oldFiddle  = $fiddleRepo->getFiddle($newFiddle->getHash(), $newFiddle->getRevision());

            $id = $oldFiddle->getId();
            if ($id) {
                foreach ($oldFiddle->getTemplates() as $template) {
                    if ($oldFiddle->getId()) {
                        $em->remove($template);
                        $em->flush($template);
                    }
                }
                $oldFiddle->clearTemplates();
                foreach ($oldFiddle->getTags() as $tag) {
                    if ($oldFiddle->getId()) {
                        $em->remove($tag);
                        $em->flush($tag);
                    }
                }
                $em->remove($oldFiddle);
                $em->flush($oldFiddle);
            }

            $em->persist($newFiddle);
            $em->flush();

            $id = $newFiddle->getId();
            $output->writeln("Imported: {$file} as fiddle ID = {$id}");
        }
    }

}
