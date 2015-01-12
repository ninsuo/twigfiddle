<?php

namespace Fuz\AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Fuz\AppBundle\Entity\FiddleTemplate;
use Fuz\AppBundle\Entity\FiddleTag;

class ExportFiddleCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("twigfiddle:export:fiddle")
           ->setDescription("Export a fiddle as a json string")
           ->addArgument('hash', InputArgument::REQUIRED, "Fiddle's hash")
           ->addArgument('revision', InputArgument::REQUIRED, "Fiddle's revision")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hash = $input->getArgument('hash');
        $revision = $input->getArgument('revision');

        $fiddle = $this
           ->getContainer()
           ->get('doctrine')
           ->getRepository('FuzAppBundle:Fiddle')
           ->getFiddle($hash, $revision)
        ;

        if (is_null($fiddle->getId()))
        {
            $output->writeln("<error>Fiddle {$hash}:{$revision} does not exist.</error>");
            return 1;
        }

        $json = array (
                'hash' => $hash,
                'revision' => $revision,
                'twig-version' => $fiddle->getTwigVersion(),
                'context' => array (
                        'format' => $fiddle->getContext() ? $fiddle->getContext()->getFormat() : null,
                        'content' => $fiddle->getContext() ? $fiddle->getContext()->getContent() : null,
                ),
                'templates' => array_map(function(FiddleTemplate $template)
                {
                    return array (
                            'filename' => $template->getFilename(),
                            'content' => $template->getContent(),
                            'is-main' => $template->isMain(),
                    );
                }, $fiddle->getTemplates()->toArray()),
                'title' => $fiddle->getTitle(),
                'tags' => array_map(function(FiddleTag $tag)
                {
                    return $tag->getTag();
                }, $fiddle->getTags()->toArray()),
        );

        $output->writeln(json_encode($json));
        return 0;
    }

}
