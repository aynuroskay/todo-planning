<?php

namespace App\Controller;

use App\Entity\Assignment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    private $cache;
    private $entityManager;

    public function __construct(CacheInterface $cache, EntityManagerInterface $entityManager)
    {
        $this->cache = $cache;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        // Redis cache control
        $plan = $this->cache->get('weeklyPlan', function (ItemInterface $item) {
            // cache duration (e.g. 5 minutes)
            $item->expiresAfter(300);

            // We get the plan from the database and cache it
            return $this->getCurrentPlan();
        });

        return $this->render('index.html.twig', [
            'plan' => $plan
        ]);
    }

    /**
     * Retrieves the current plan from the database and returns it, organized by developer.
     */
    private function getCurrentPlan(): array
    {
        $assignments = $this->entityManager->getRepository(Assignment::class)->findAll();
        $weeklyHours = 45;
        $plan = [];
        $developerHours = []; // remaining hours for each developer

        foreach ($assignments as $assignment) {
            $developer = $assignment->getDeveloper()->getName();
            $task = $assignment->getTask()->getName();
            $duration = $assignment->getTask()->getEstimatedDuration();
            $difficulty = $assignment->getTask()->getDifficulty();
            $provider = $assignment->getTask()->getProvider();

            // If the developer is seen for the first time, initialize their remaining time to the weekly working hours
            if (!isset($developerHours[$developer])) {
                $developerHours[$developer] = $weeklyHours;
            }

            // Deduct the task time from the developer's remaining hours
            $developerHours[$developer] -= $duration;

            // Creating a task list for a developer
            $plan[$developer]['tasks'][] = [
                'task' => $task,
                'duration' => $duration,
                'difficulty' => $difficulty,
                'provider' => $provider
            ];

            // add the developer's remaining time
            $plan[$developer]['remaining_hours'] = $developerHours[$developer];
        }

        return $plan;
    }
}