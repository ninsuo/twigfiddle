<?php

namespace Fuz\AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ExportSamplesCommand extends ContainerAwareCommand
{

   const EXPORT_COMMAND = 'twigfiddle:export:fiddle';

   protected function configure()
   {
      parent::configure();
      $this
         ->setName("twigfiddle:export:samples")
         ->setDescription("Export all samples as json in the given directory")
         ->addArgument('directory', InputArgument::REQUIRED, "Samples directory")
      ;
   }

   protected function execute(InputInterface $in, OutputInterface $out)
   {
      $directory = $in->getArgument('directory');
      if (!is_dir($directory) || !is_writeable($directory))
      {
         $out->writeln("Directory {$directory} does not exist or is not writeable.");
         return 1;
      }

      $command = $this->getApplication()->find(self::EXPORT_COMMAND);

      $config = $this->getContainer()->getParameter('web');
      foreach ($config['samples'] as $group)
      {
         foreach ($group as $fiddle)
         {
            $arguments = array (
                   self::EXPORT_COMMAND,
                   'hash' => $fiddle['hash'],
                   'revision' => $fiddle['revision'],
            );
            $input = new ArrayInput($arguments);
            $output = new BufferedOutput();
            try
            {
               $ret = $command->run($input, $output);
            }
            catch (Exception $ex)
            {
               $ret = $ex;
            }

            if ($ret)
            {
               $out->writeln("<error>KO</error>\tFailed to export {$fiddle['hash']}:{$fiddle['revision']}:");
               $out->write("\t -> " . $output->fetch());
               continue;
            }

            $file = "{$directory}/{$fiddle['hash']}.{$fiddle['revision']}.json";
            file_put_contents($file, $output->fetch());
            $out->writeln("<info>OK</info>\tExported {$fiddle['hash']}:{$fiddle['revision']} to {$file}.");
         }
      }
   }

}
