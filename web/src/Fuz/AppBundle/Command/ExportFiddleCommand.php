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

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $json       = $serializer->serialize($fiddle, 'json');

        $output->writeln($json);

        return 0;
    }
}
