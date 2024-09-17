<?php

namespace App\Service\Provider;

use App\Entity\Task;

interface TaskProviderInterface
{
    /**
     * @return Task[]
     */
    public function fetchTasks(): array;
}