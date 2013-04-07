<?php

namespace Sm\TaskBundle\Controller;

use Sm\TaskBundle\Model\TaskModel;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sm\TaskBundle\Entity\Task;
use Sm\TaskBundle\Form\TaskType;

/**
 * Task controller.
 *
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * Lists all Task entities.
     *
     * @Route("/", name="task")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $pagination = $this->get('knp_paginator')->paginate(
            $em->getRepository('SmTaskBundle:Task')->getPaginationQuery(),
            $request->get('page', 1),
            10
        );

        $data = array('pagination' => $pagination);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SmTaskBundle:Task:list.html.twig', $data);
        }
        return $data;
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/", name="task_create", defaults={"_format" = "json"})
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity  = new Task();
        return $this->_save($entity);
    }

    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}", name="task_update", defaults={"_format" = "json"})
     * @Method("POST")
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->_getEntity($id);
        return $this->_save($entity);
    }

    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}/complete", name="task_complete", defaults={"_format" = "json"})
     * @Method("POST")
     */
    public function completeAction(Request $request, $id)
    {
        $entity = $this->_getEntity($id);
        $entity->setCompletedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', 'Task marked as complete successfully');

        return array();
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new", name="task_new")
     * @Method("GET")
     * @Template("SmTaskBundle:Task:create.html.twig")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();

        $model = new TaskModel($em, new Task());

        $form   = $this->createForm(new TaskType(), $model);

        return array(
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Method("GET")
     * @Template("SmTaskBundle:Task:edit.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $model = new TaskModel($em, $this->_getEntity($id));

        $editForm = $this->createForm(new TaskType(), $model);

        return array(
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}/delete", name="task_delete", defaults={"_format" = "json"})
     */
    public function deleteAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {
            $entity = $this->_getEntity($id);

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Task deleted successfully');
        }
        return new JsonResponse();
    }

    /**
     * Get entity
     *
     * @param integer $id
     * @return Task
     */
    private function _getEntity($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SmTaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }
        return $entity;
    }

    /**
     * Save entity
     *
     * @param Task $entity
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function _save($entity)
    {
        $em = $this->getDoctrine()->getManager();

        $model = new TaskModel($em, new Task());

        $form = $this->createForm(new TaskType(), $model);
        $form->bind($this->getRequest());

        $success = false;
        $template = '';

        if ($form->isValid()) {
            $model->save();

            $success = true;
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'Task saved successfully');
        } else {
            $view = 'SmTaskBundle:Task:create.html.twig';
            if ($entity->getId()) {
                $view = 'SmTaskBundle:Task:edit.html.twig';
            }
            $template = $this->renderView($view, array('form' => $form->createView(), 'id' => $entity->getId()));
        }
        return new JsonResponse(array('form' => $template, 'success' => $success));
    }
}
