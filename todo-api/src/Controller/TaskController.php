<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/api/tasks', methods: ['GET'])]
    public function getTasks(EntityManagerInterface $em): JsonResponse
    {
        $tasks = $em->getRepository(Task::class)->findAll();
        return $this->json($tasks);
    }

    #[Route('/api/tasks', methods: ['POST'])]
    public function createTask(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = new Task();
        $task->setDescription($data['description']);
        $em->persist($task);
        $em->flush();
        return $this->json($task);
    }

    #[Route('/api/tasks/{id}', methods: ['PUT'])]
    public function updateTask($id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);
        if (!$task) {
            return $this->json(['message' => 'Task not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['isCompleted'])) {
            $task->setIsCompleted($data['isCompleted']);
        }
        $em->flush();
        return $this->json($task);
    }

    #[Route('/api/tasks/{id}', methods: ['DELETE'])]
    public function deleteTask($id, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);
        if (!$task) {
            return $this->json(['message' => 'Task not found'], 404);
        }
        $em->remove($task);
        $em->flush();
        return $this->json(['message' => 'Task deleted']);
    }
}
