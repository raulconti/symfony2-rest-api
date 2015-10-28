<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Task;
use AppBundle\Form\Handler\FormHandler;
use AppBundle\Model\TaskInterface;
use AppBundle\Model\ProjectInterface;
use AppBundle\Repository\TaskRepository;

class TaskHandler
{
    private $repository;
    private $formHandler;

    public function __construct(TaskRepository $taskRepository, FormHandler $formHandler)
    {
        $this->repository = $taskRepository;
        $this->formHandler = $formHandler;
    }

    /**
     * @param $id
     * @param $project
     * @return mixed
     */
    public function get($id, $project)
    {
        return $this->repository->findOneBy(
            array('id' => $id, 'project' => $project)
        );
    }

    /**
     * @param $project
     * @param $limit
     * @param $offset
     * @return array
     */
    public function all($project, $limit, $offset)
    {
        return $this->repository->findBy(array('project' => $project), array(), $limit, $offset);
    }

    /**
     * @param ProjectInterface $project
     * @param array $parameters
     * @return mixed
     */
    public function post(ProjectInterface $project, array $parameters)
    {
        $task = $this->formHandler->processForm(
            new Task(),
            $parameters,
            array("method" => "POST", "persist" => false)
        );

        $task->setProject($project);
        $this->formHandler->persist($task);

        return $task;
    }

    /**
     * @param TaskInterface $taskInterface
     * @param ProjectInterface $project
     * @param array $parameters
     * @return mixed
     */
    public function put(TaskInterface $taskInterface, ProjectInterface $project, array $parameters)
    {
        $task = $this->formHandler->processForm(
            $taskInterface,
            $parameters,
            array("method" => "PUT", "persist" => false)
        );

        $task->setProject($project);
        $this->formHandler->persist($task);

        return $task;
    }

    /**
     * @param TaskInterface $taskInterface
     * @param ProjectInterface $project
     * @param array $parameters
     * @return mixed
     */
    public function patch(TaskInterface $taskInterface, ProjectInterface $project, array $parameters)
    {
        $task = $this->formHandler->processForm(
            $taskInterface,
            $parameters,
            array("method" => "PATCH", "persist" => false)
        );

        $task->setProject($project);
        $this->formHandler->persist($task);

        return $task;
    }

    /**
     * @param TaskInterface $taskInterface
     * @return mixed
     */
    public function delete(TaskInterface $taskInterface)
    {
        return $this->formHandler->delete($taskInterface);
    }
}