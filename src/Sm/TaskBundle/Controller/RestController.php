<?php

namespace Sm\TaskBundle\Controller;

use Sm\TaskBundle\Model\TaskModel;

use Symfony\Component\HttpFoundation\Request;

use Sm\TaskBundle\Entity\Task;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Task controller.
 *
 * @Route("/api/task", defaults={"_format"="json"})
 */
class RestController extends Controller
{
    /**
     * @Rest\View(
     *     serializerGroups={"task", "task_priority", "priority"}
     * )
     *
     * @Route()
     * @Method({"GET"})
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get list of activities",
     *  output="Sm\TaskBundle\Entity\Task",
     *  filters={
     *      {"name"="limit",    "type"="integer", "default" = 25},
     *      {"name"="offset",   "type"="integer", "default" = 0},
     *  }
     * )
     */
    public function allAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SmTaskBundle:Task')->findBy(
            array(),
            array(),
            $request->get('limit', 10),
            $request->get('offset', 0)
        );

        return array(
            'tasks' => $entities,
        );
    }

    /**
     * @Rest\View(
     *     statusCode=201
     * )
     *
     * @Route()
     * @Method({"POST"})
     *
     * @ApiDoc(
     *  description="Create new task",
     *  input="Sm\TaskBundle\Model\TaskModel"
     *
     * )
     */
    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $model = new TaskModel($em, new Task());
        $model->bindRequest($request);

        $validator = $this->get('validator');
        $errors = $validator->validate($model);

        if ($errors->count()) {
            return View::create(
                $this->get('sm_core.validation_errors_formatter')->format($errors),
                400
            );
        }
        $model->save();

        return array();
    }

    /**
     * @Rest\View(
     *     statusCode=200
     * )
     *
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method({"PUT"})
     *
     * @ApiDoc(
     *  description="Update a task",
     *  input="Sm\TaskBundle\Model\TaskModel"
     *
     * )
     */
    public function putAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->_getEntity($id);

        $model = new TaskModel($em, $entity);
        $model->bindRequest($request);

        $validator = $this->get('validator');
        $errors = $validator->validate($model);

        if ($errors->count()) {
            return View::create(
                $this->get('sm_core.validation_errors_formatter')->format($errors),
                400
            );
        }
        $model->save();

        return $model;
    }

    /**
     * @Rest\View(
     *     statusCode=204
     * )
     *
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method({"DELETE"})
     *
     * @ApiDoc(
     *  description="Delete a task"
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->_getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
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
}
