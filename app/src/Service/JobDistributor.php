<?php

namespace App\Service;

use App\Messenger\Message\DistributeJobsMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class JobDistributor
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * To start job distribution asynchronously, first pull the tasks.
     */
    public function distributeJobsAsync()
    {
        $this->bus->dispatch(new DistributeJobsMessage('Fetch Tasks and Distribute'));
    }
}