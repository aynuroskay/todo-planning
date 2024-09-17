<?php

namespace App\Service;

use App\Entity\Task;
use App\Service\Provider\TaskProviderInterface;
use App\Entity\Developer;
use Doctrine\ORM\EntityManagerInterface;

class TaskManager
{
    private $entityManager;
    private $providers = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TaskProviderInterface $provider
     */
    public function addProvider(TaskProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * It pulls tasks from all providers and saves them in the database.
     */
    public function fetchAllTasks(): void
    {
        foreach ($this->providers as $provider) {
            $tasks = $provider->fetchTasks();
            foreach ($tasks as $task) {
                // check if the task already exists in the database
                $existingTask = $this->entityManager->getRepository(Task::class)->findOneBy(['externalId' => $task->getExternalId()]);

                if (!$existingTask) {
                    $this->entityManager->persist($task);
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * all tasks from the database.
     */
    public function getTasks(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }

    /**
     * all developers from the database.
     */
    public function getDevelopers(): array
    {
        return $this->entityManager->getRepository(Developer::class)->findAll();
    }

    /**
     * Deletes all records from the database.
     */
    public function clearAssignments()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Assignment')->execute();
    }
}
