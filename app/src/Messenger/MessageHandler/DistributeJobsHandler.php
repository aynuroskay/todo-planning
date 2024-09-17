<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Assignment;
use App\Entity\Developer;
use App\Entity\Task;
use App\Messenger\Message\DistributeJobsMessage;
use App\Service\Provider\TaskProviderInterface;
use App\Service\TaskManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DistributeJobsHandler implements MessageHandlerInterface
{
    private $taskManager;
    private $entityManager;

    public function __construct(TaskManager $taskManager, EntityManagerInterface $entityManager)
    {
        $this->taskManager = $taskManager;
        $this->entityManager = $entityManager;
    }

    public function __invoke(DistributeJobsMessage $message)
    {
        $developers = $this->taskManager->getDevelopers();
        $tasks = $this->taskManager->getTasks();

        $developerHours = [];
        foreach ($developers as $developer) {
            $developerHours[$developer->getId()] = 45;
        }

        // all assignments
        $assignments = $this->entityManager->getRepository(Assignment::class)->findAll();

        foreach ($assignments as $assignment) {
            $developer = $assignment->getDeveloper();
            $task = $assignment->getTask();
            $developerHours[$developer->getId()] -= $task->getEstimatedDuration();

            // We remove existing tasks from the task list for reassignment
            $tasks = array_filter($tasks, fn($t) => $t->getId() !== $task->getId());
        }

        // Sort tasks by difficulty (most difficult tasks are assigned first)
        usort($tasks, function ($a, $b) {
            return $b->getDifficulty() <=> $a->getDifficulty();
        });

        // new tasks distribution
        foreach ($tasks as $task) {
            $bestDeveloper = $this->findBestDeveloper($developers, $developerHours, $task);

            if ($bestDeveloper) {
                // Developer is assigned the task
                $this->assignTask($bestDeveloper, $task);

                // Deduct task time from developer's remaining hours
                $developerHours[$bestDeveloper->getId()] -= $task->getEstimatedDuration();
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Finds the best developer for a task
     */
    private function findBestDeveloper($developers, $developerHours, $task)
    {
        $bestDeveloper = null;
        $maxRemainingHours = 0;

        foreach ($developers as $developer) {
            $remainingHours = $developerHours[$developer->getId()];

            if ($remainingHours >= $task->getEstimatedDuration() && $remainingHours > $maxRemainingHours) {
                $bestDeveloper = $developer;
                $maxRemainingHours = $remainingHours;
            }
        }

        return $bestDeveloper;
    }

    /**
     * Assigns a task to a developer
     */
    private function assignTask(Developer $developer, Task $task): void
    {
        $assignment = new Assignment();
        $assignment->setDeveloper($developer);
        $assignment->setTask($task);
        $assignment->setWeek(1);

        $this->entityManager->persist($assignment);
    }
}
