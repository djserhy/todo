<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TodoController extends Controller
{
    /**
     * @Route("/", name="todo")
     */
    public function index()
    {
        $todos = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->findAll();

        dump($todos);

        return $this->render('todo/index.html.twig', [
            'controller_name' => 'TodoController',
            'todos' => $todos
        ]);
    }

    /**
     * @Route("/view/{id}", name="todo_view")
     * @param $id
     */
    public function view($id)
    {
        $todo = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->find($id);

        dump($todo);

        return $this->render('todo/view.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * @Route("/edit/{id}", name="todo_edit")
     */
    public function edit($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $todo = $entityManager
            ->getRepository(Todo::class)
            ->find($id);

        $form = $this->createForm(TodoType::class, $todo);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $todo->setName($data->getName());
            $todo->setCategory($data->getCategory());
            $todo->setDescription($data->getDescription());
            $todo->setDueDate($data->getDueDate());
            $todo->setCreateDate($data->getCreateDate());

            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute('todo_view', [
                'id' => $id
            ]);
        }

        dump($todo);

        return $this->render('todo/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="todo_delete")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $todo = $entityManager
            ->getRepository(Todo::class)
            ->find($id);

        $entityManager->remove($todo);

        $entityManager->flush();

        return $this->redirectToRoute('todo');
    }

    /**
     * @Route("/create", name="todo_create")
     */
    public function create(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(TodoType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $todo = new Todo();
            $todo->setName($data->getName());
            $todo->setCategory($data->getCategory());
            $todo->setDescription($data->getDescription());
            $todo->setDueDate($data->getDueDate());
            $todo->setCreateDate($data->getCreateDate());

            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute("todo");
        }

        return $this->render('todo/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}