<?php

namespace AppBundle\Controller;

use AppBundle\Exception\InvalidFormException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\ProjectType;
use AppBundle\Entity\Project;


class ProjectController extends FOSRestController
{
    /**
     * Return a project when given a valid id
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieves a Project by id",
     *  output="AppBundle\Entity\Project",
     *  section="Projects",
     *  statusCodes={
     *      200="Returned when succesfull",
     *      404="Returned when the requested Project is not found"
     *  }
     * )
     *
     * @View()
     *
     * @param int $id   The Project id
     *
     * @return array
     */
    public function getProjectAction($id)
    {
        return $this->getOrError($id);
    }

    /**
     * Returns a collection of Projects filtered by optional criteria
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of Projects",
     *  section="Projects",
     *  requirements={
     *      {"name"="limit", "dataType"="integer", "requirement"="\d+", "description"="the max number of records to return"}
     *  },
     *  parameters={
     *      {"name"="limit", "dataType"="integer", "required"=true, "description"="the max number of records to return"},
     *      {"name"="offset", "dataType"="integer", "required"=false, "description"="the record number to start results at"}
     *  }
     * )
     *
     * @QueryParam(name="limit", requirements="\d+", default="10", description="our limit")
     * @QueryParam(name="offset", requirements="\d+", nullable=true, default="0", description="our offset")
     *
     * @return mixed
     */
    public function getProjectsAction(ParamFetcherInterface $paramFetcher)
    {
        $limit = $paramFetcher->get('limit');
        $offset = $paramFetcher->get('offset');

        return $this->getHandler()->all($limit, $offset);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Creates a new Project",
     *  input = "AppBundle\Form\Type\ProjectType",
     *  output = "AppBundle\Entity\Project",
     *  section="Projects",
     *  statusCodes={
     *         201="Returned when a new Project has been successfully created",
     *         400="Returned when the posted data is invalid"
     *     }
     * )
     *
     * @View()
     *
     * @param Request $request
     * @return array|\FOS\RestBundle\View\View|null
     */
    public function postProjectAction(Request $request)
    {
        try {
            $project = $this->getHandler()->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id'        => $project->getId(),
                '_format'   => $request->get('_format'),
            );

            return $this->routeRedirectView(
                'get_project',
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
     *  description="Replaces an existing Project",
     *  input = "AppBundle\Form\Type\ProjectType",
     *  output = "AppBundle\Entity\Project",
     *  section="Projects",
     *  statusCodes={
     *         201="Returned when a new Project has been successfully created",
     *         204="Returned when an existing Project has been successfully updated",
     *         400="Returned when the posted data is invalid"
     *     }
     * )
     *
     * @param Request $request
     * @param int $id   The Project id
     * @return array|\FOS\RestBundle\View\View|null
     */
    public function putProjectAction(Request $request, $id)
    {
        $project = $this->getHandler()->get($id);

        if ($project->getUser() != $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        try {

            if ($project === null) {
                $statusCode = Response::HTTP_CREATED;
                $project = $this->getHandler()->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Response::HTTP_NO_CONTENT;
                $project = $this->getHandler()->put(
                    $project,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id'        => $project->getId(),
                '_format'   => $request->get('_format')
            );

            return $this->routeRedirectView('get_project', $routeOptions, $statusCode);

        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Updates an existing Project",
     *  input = "AppBundle\Form\Type\ProjectType",
     *  output = "AppBundle\Entity\Project",
     *  section="Projects",
     *  statusCodes={
     *         204="Returned when an existing Project has been successfully updated",
     *         400="Returned when the posted data is invalid",
     *         404="Returned when trying to update a non existent Project"
     *     }
     * )
     *
     * @param Request $request
     * @param int $id   The Project id
     * @return array|\FOS\RestBundle\View\View|null
     */
    public function patchProjectAction(Request $request, $id)
    {
        try {
            $project = $this->getHandler()->patch(
                $this->getOrError($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id'        => $project->getId(),
                '_format'   => $request->get('_format')
            );

            return $this->routeRedirectView('get_project', $routeOptions, Response::HTTP_NO_CONTENT);

        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Deletes an existing Project",
     *  section="Projects",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="the id of the Project to delete"}
     *  },
     *  statusCodes={
     *         204="Returned when an existing Project has been successfully deleted",
     *         404="Returned when trying to delete a non existent Project"
     *     }
     * )
     *
     * @param Request $request
     * @param int $id   The Project id
     */
    public function deleteProjectAction(Request $request, $id)
    {
        $project = $this->getOrError($id);

        $this->getHandler()->delete($project);
    }

    /**
     * Returns a record by id or throws a 404 error
     * Checks if the resource belongs to the authenticated
     *
     * @param int $id   The Project id
     * @return mixed
     */
    protected function getOrError($id)
    {
        $handler = $this->getHandler();
        $data = $handler->get($id);

        if(null === $data){
            throw new NotFoundHttpException();
        }

        if ($data->getUser() != $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $data;
    }

    private function getHandler()
    {
        return $this->get('app_bundle.project_handler');
    }
}
