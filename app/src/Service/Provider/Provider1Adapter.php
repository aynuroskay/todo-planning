<?php

namespace App\Service\Provider;

use App\Entity\Task;
use Symfony\Component\HttpClient\HttpClient;

class Provider1Adapter implements TaskProviderInterface
{
    private const PROVIDER_NAME = 'Provider 1';
    private const PROVIDER_URL = 'https://raw.githubusercontent.com/WEG-Technology/mock/main/mock-one';

    public function fetchTasks(): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', self::PROVIDER_URL);
        $tasksData = $response->toArray();
        $tasks = [];

        foreach ($tasksData as $taskData) {
            $task = new Task();
            $task->setName('Task #' . $taskData['id']);
            $task->setDifficulty($taskData['value']);
            $task->setEstimatedDuration($taskData['estimated_duration']);
            $task->setExternalId($taskData['id']);
            $task->setProvider(self::PROVIDER_NAME);
            $tasks[] = $task;
        }

        return $tasks;
    }
}