<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use ContainerDDtYuU0\getDoctrine_UlidGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TodoController extends AbstractController
{
    private $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    /**
     * @Route("/todo/", name="add_todo", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $title = $data['title'];
        $detail = $data['details'];

        if (empty($title) || empty($detail)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->todoRepository->saveTodo($title, $detail);

        return new JsonResponse(['status' => 'Todo created!'], Response::HTTP_CREATED);
    }


    /**
     * @Route("/todo/{id}", name="get_one_todo", methods={"GET"})
     */
    public function get($id , EntityManagerInterface $entityManager , CacheInterface $todocache): JsonResponse
    {
        $todo = $todocache->get($id , function(ItemInterface $item) use ($id ,$entityManager){
            $item->expiresAt(date_create('tomorrow'));

            $todo = $this->todoRepository->findOneBy(['id' => $id]);

            $data = [
                    'id' => $todo->getId(),
                    'title' => $todo->getTitle(),
                    'details' => $todo->getDetails(),
                
                ];
            return $data;
        });

        return new JsonResponse($todo, Response::HTTP_OK);
    }


    /**
     * @Route("/todoall", name="get_all_todo", methods={"GET"})
     */
    public function getall(Request $request): Response
    {
        $todo = $this->todoRepository->findAll();
        // $todos = $this->getDoctrine()->getRepository(Todo::class)->findAll();
        return $this->json($todo);
    }

    /**
     * @Route("/todo/{id}", name="update_todo", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $todo = $this->todoRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['title']) ? true : $todo->setTitle($data['title']);
        empty($data['details']) ? true : $todo->setDetails($data['details']);

        $updatedCostumer = $this->todoRepository->updateTodo($todo);

        return new JsonResponse($updatedCostumer->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/todo/{id}", name="delete_todo", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $todo = $this->todoRepository->findOneBy(['id' => $id]);

        $this->todoRepository->removeTodo($todo);

        return new JsonResponse(['status' => 'Todo item deleted'], Response::HTTP_NO_CONTENT);
    }
}
