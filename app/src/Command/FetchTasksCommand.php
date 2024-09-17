<?php

namespace App\Command;

use App\Messenger\Message\FetchTasksMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class FetchTasksCommand extends Command
{
    protected static $defaultName = 'app:fetch-tasks';
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setDescription('Fetch tasks from providers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->bus->dispatch(new FetchTasksMessage('Fetch tasks from providers'));

        $io->success('Fetch tasks işlemi asenkron olarak başlatıldı.');

        return 0;
    }
}
