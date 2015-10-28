<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Exception\InvalidFormException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\TaskType;
use AppBundle\Entity\Task;


class TaskController extends FOSRestController
{

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieves a Task by id",
     *  output="AppBundle\Entity\Task",
     *  section="Tasks",
     *  statusCodes={
     *      200="Returned when succesfull",
     *      404="Returned when the requested Task is not found"
     *  }
     * )
     * @View()
     *
     * @param $project
     * @param int $id   The Task id
     *
     * @return array
     */
    public function getTaskAction(Project $project, $id)
    {
        return $this->getOrError($id, $project);
    }

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of Tasks",
     *  section="Tasks",
     *  requirements={
     *      {"name"="limit", "dataType"="integer", "requirement"="\d+", "description"="the max number of records to return"}
     *  },
     *  parameters={
     *      {"name"="limit", "dataType"="integer", "required"=true, "description"="the max number of records to return"},
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="the record number to start results at"}
     *  }
     * )
     *
     * @param $project
     *
     * @QueryParam(name="limit", requirements="\d+", default="10", description="our limit")
     * @QueryParam(name="offset", requirements="\d+", nullable=true, default="0", description="our offset")
     *
     * @return mixed
     */
    public function getTasksAction(ParamFetcherInterface $paramFetcher, Project $project)
    {
        if ($project->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $limit = $paramFetcher->get('limit');
        $offset = $paramFetcher->get('offset');

        return $this->getHandler()->all($project, $limit, $offset);
    }


    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Creates a new Task",
     *  input = "AppBundle\Form\Type\TaskType",
     *  output = "AppBundle\Entity\Task",
     *  section="Tasks",
     *  statusCodes={
     *         201="Returned when a new Task has been successfully created",
     *         400="Returned when the posted data is invalid"
     *     }
     * )
     *
     * @View()
     *
     * @param Request $request
     * @param $project
     *
     * @return array|\FOS\RestBundle\View\View|null
     */
    public function postTaskAction(Request $request, Project $project)
    {
        if(null === $project){
            throw new NotFoundHttpException();
        }

        if ($project->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        try {
            $task = $this->getHandler()->post(
                $project,
                $request->request->all()
            );

            $routeOptions = array(
                'id'        => $task->getId(),
                'project'   => $project->getId(),
                '_format'   => $request->get('_format'),
            );

            return $this->routeRedirectView(
                'get_project_task',
                $routeOptions,
                Response::HTTP_CREATED
            );

        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Replaces an existing Task",
     *  input = "AppBundle\Form\Type\TaskType",
     *  output = "AppBundle\Entity\Task",
     *  section="Tasks",
     *  statusCodes={
     *         201="Returned when a new Task has been successfully created",
     *         204="Returned when an existing Task has been successfully updated",
     *         400="Returned when the posted data is invalid"
     *     }
     * )
     *
     * @param Request $request
     * @param $project
     * @param int $id   The Task id
     *
     * @return array|\FOS\RestBundle\View\View|null
     */
    public function putTaskAction(Request $request, Project $project, $id)
    {
        if(null === $project){
            throw new NotFoundHttpException();
        }

        if ($project->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $task = $this->getHandler()->get($id, $project);

        try {

            if ($task === null) {
                $statusCode = Response::HTTP_CREATED;
                $task = $this->getHandler()->post(
                    $project,
                    $request->request->all()
                );
            } else {
                $statusCode = Response::HTTP_NO_CONTENT;
                $task = $this->getHandler()->put(
                    $task,
                    $project,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id'        => $task->getId(),
                'project'   => $project->getId(),
                '_format'   => $request->get('_format')
            );

            return $this->routeRedirectView('get_project_task', $routeOptions, $statusCode);

        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Updates an existing Task",
     *  input = "AppBundle\Form\Type\TaskType",
     *  output = "AppBundle\Entity\Task",
     *  section="Tasks",
     *  statusCodes={
     *         204="Returned when an existing Task has been successfully updated",
     *         400="Returned when the posted data is invalid",
     *         404="Returned when trying to update a non existent Task"
     *     }
     * )
     *
     * @param Request $request
     * @param $project
     * @param int $id   The Task id
     *
     * @return array|\FOS\RestBundle\View\View|null
     */
    public function patchTaskAction(Request $request, Project $project, $id)
    {
        if(null === $project){
            throw new NotFoundHttpException();
        }

        if ($project->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        try {
            $task = $this->getHandler()->patch(
                $this->getOrError($id, $project),
                $project,
                $request->request->all()
            );

            $routeOptions = array(
                'id'        => $task->getId(),
                'project'   => $project->getId(),
                '_format'   => $request->get('_format')
            );

            return $this->routeRedirectView('get_project_task', $routeOptions, Response::HTTP_NO_CONTENT);

        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Deletes an existing Task",
     *  section="Tasks",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="the id of the Task to delete"}
     *  },
     *  statusCodes={
     *         204="Returned when an existing Task has been successfully deleted",
     *         404="Returned when trying to delete a non existent Task"
     *     }
     * )
     *
     * @param Request $request
     * @param $project
     * @param int $id   The Project id
     */
    public function deleteTaskAction(Request $request, Project $project, $id)
    {
        $task = $this->getOrError($id, $project);

        $this->getHandler()->delete($task);
    }

    /**
     * Returns a record by task id and project id, or throws a 404 error
     * Checks if the resource belongs to the authenticated
     *
     * @param int $id   The Project id
     * @param $project
     * @return mixed
     */
    protected function getOrError($id, Project $project)
    {
        if ($project->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $handler = $this->getHandler();
        $data = $handler->get($id, $project);

        if(null === $data){
            throw new NotFoundHttpException();
        }

        return $data;
    }

    private function getHandler()
    {
        return $this->get('app_bundle.task_handler');
    }
}
