<?php

namespace AppBundle\Model;

interface TaskInterface
{
    /**
     * Returns a Task's title
     *
     * @return mixed
     */
    public function getTitle();

    /**
     * Returns a Task's notes
     *
     * @return mixed
     */
    public function getNotes();

    /**
     * Returns a Task's date of creation
     *
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * Returns a Task's date of modification
     *
     * @return mixed
     */
    public function getModifiedAt();

    /**
     * Returns a Task's status
     *
     * @return mixed
     */
    public function getStatus();

    /**
     * Returns a Task's date of due
     *
     * @return mixed
     */
    public function getDue();

    /**
     * Returns a Task's date of completed
     *
     * @return mixed
     */
    public function getCompleted();
}