<?php

namespace App\Command;

use App\Service\JobDistributor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DistributeJobsCommand extends Command
{
    protected static $defaultName = 'app:distribute-jobs';
    private $jobDistributor;

    public function __construct(JobDistributor $jobDistributor)
    {
        parent::__construct();
        $this->jobDistributor = $jobDistributor;
    }

    protected function configure()
    {
        $this
            ->setDescription('Asynchronously fetches tasks and starts job distribution.')
            ->setHelp('This command fetches tasks first, then starts job distribution asynchronously.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->jobDistributor->distributeJobsAsync();

        $io->success('Tasks started to be pulled asynchronously.');

        return 0;
    }
}

