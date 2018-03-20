<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\Type\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class TaskController extends Controller
{
    /**
     * @Route("/tasks/list/{isDone}", name="task_list")
     * @Method({"GET"})
     * @Cache(smaxage="86400", public=true)
     */
    public function listAction($isDone=0)
    {
        return $this->render('task/list.html.twig', ['isDone'=>$isDone,'tasks' => $this->getDoctrine()->getRepository('AppBundle:Task')->findByIsDone($isDone)]);
    }


    /**
     * @Route("/tasks/create", name="task_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $task->setAuthor($this->getUser());
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     * @Method({"GET","POST"})
     */
    public function toggleTaskAction(Task $task)
    {
        $status=($task->isDone()==0)?0:1;
        $message=($task->isDone()==0)?'comme faite':'comme non terminée';
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche "%s" a bien été marquée '.$message.'.', $task->getTitle()));

        return $this->redirectToRoute('task_list', array('isDone' => $status));
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @Method({"GET", "DELETE"})
     * @Security("user == task.getAuthor()")
     */
    public function deleteTaskAction(Task $task)
    {
        $status=($task->isDone()==0)?0:1;

        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list', array('isDone' => $status));
    }
}
