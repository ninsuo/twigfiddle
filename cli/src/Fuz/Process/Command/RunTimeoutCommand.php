<?php

namespace Fuz\Process\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;
use Fuz\Framework\Base\BaseCommand;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;

class RunTimeoutCommand extends BaseCommand
{

    protected $environmentId;
    protected $timeout;
    protected $process;

    protected function configure()
    {
        parent::configure();
        $this
           ->setName("twigfiddle:run:timeout")
           ->setDescription("Executes a twigfiddle (from already prepared environment) with a timeout")
           ->addArgument('environment-id', InputArgument::REQUIRED,
              "Environment where the twigfiddle is stored and will be executed")
           ->addArgument('timeout', InputArgument::REQUIRED, "The fiddle's maximum execution time (seconds)")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
           ->initArguments($input)
           ->initProcess()
           ->runProcess()
        ;

        $output->write('');
    }

    protected function initArguments(InputInterface $input)
    {
        $this->environmentId = $input->getArgument('environment-id');
        $this->timeout = $input->getArgument('timeout');
        return $this;
    }

    protected function initProcess()
    {
        $command = array (
                '/usr/bin/php',
                $this->getParameter('root_dir') . '/run-' . $this->getParameter('env') . '.php',
                'twigfiddle:run',
                $this->environmentId,
        );

        $builder = new ProcessBuilder($command);
        $builder->setTimeout($this->timeout);

        $this->process = $builder->getProcess();
        $this->process->disableOutput();

        return $this;
    }

    protected function runProcess()
    {
        $this->process->start();
        try
        {
            while ($this->process->isRunning())
            {
                $this->process->checkTimeout();
                usleep(200000);
            }
        }
        catch (\RuntimeException $e)
        {
            $this->saveError(Error::E_TIMEOUT, $e);
        }
        catch (StopExecutionException $e)
        {
            $this->saveError(Error::E_UNEXPECTED, $e);
        }
        return $this;
    }

    public function saveError($errno, \Exception $e)
    {
        $agent = $this
           ->get('fiddle_agent')
           ->setEnvironmentId($this->environmentId)
           ->addError($errno, array ('Exception' => $e))
        ;

        $this->get('environment_manager')->prepareEnvironment($agent);

        $this
           ->get('shared_memory_manager')
           ->recoverSharedMemory($agent, false)
           ->saveResults($agent);
    }

}
