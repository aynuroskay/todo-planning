<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Task;
use App\Messenger\Message\FetchTasksMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\TaskManager;

class FetchTasksHandler implements MessageHandlerInterface
{
    private $taskManager;

    public function __construct(TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
    }

    public function __invoke(FetchTasksMessage $message)
    {
        // Mesajı işleyip sağlayıcılardan görevleri çekiyoruz
        $this->taskManager->fetchAllTasks();

        // Görev çekme işlemi tamamlandıktan sonra işlem mesajı basılıyor
        echo 'Tasks fetched and saved to DB: ' . $message->getContent();
    }
}