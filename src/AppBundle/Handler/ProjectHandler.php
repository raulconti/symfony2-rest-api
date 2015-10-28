<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Project;
use AppBundle\Form\Handler\FormHandler;
use AppBundle\Model\ProjectInterface;
use AppBundle\Repository\ProjectRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProjectHandler
{
    private $repository;
    private $formHandler;

    public function __construct(ProjectRepository $projectRepository, FormHandler $formHandler, TokenStorageInterface $token_storage)
    {
        $this->repository = $projectRepository;
        $this->formHandler = $formHandler;
        $this->token_storage = $token_storage;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->repository->findOneBy(
            array('id' => $id)
        );
    }

    /**
     * @param $limit
     * @param $offset
     * @return array
     */
    public function all($limit, $offset)
    {
        $user = $this->token_storage->getToken()->getUser();

        return $this->repository->findBy(array('user' => $user), array(), $limit, $offset);
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    public function post(array $parameters)
    {
        $user = $this->token_storage->getToken()->getUser();

        $project = $this->formHandler->processForm(
            new Project(),
            $parameters,
            array("method" => "POST", "persist" => false)
        );

        $project->setUser($user);
        $this->formHandler->persist($project);

        return $project;
    }

    /**
     * @param ProjectInterface $projectInterface
     * @param array $parameters
     * @return mixed
     */
    public function put(ProjectInterface $projectInterface, array $parameters)
    {
        return $this->formHandler->processForm(
            $projectInterface,
            $parameters,
            array("method" => "PUT", "persist" => true)
        );
    }

    /**
     * @param ProjectInterface $projectInterface
     * @param array $parameters
     * @return mixed
     */
    public function patch(ProjectInterface $projectInterface, array $parameters)
    {
        return $this->formHandler->processForm(
            $projectInterface,
            $parameters,
            array("method" => "PATCH", "persist" => true)
        );
    }

    /**
     * @param ProjectInterface $projectInterface
     * @return mixed
     */
    public function delete(ProjectInterface $projectInterface)
    {
        return $this->formHandler->delete($projectInterface);
    }
}