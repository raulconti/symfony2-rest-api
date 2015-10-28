<?php

namespace AppBundle\Model;

interface ProjectInterface
{
    /**
     * Returns a Project title
     *
     * @return mixed
     */
    public function getTitle();

    /**
     * Returns a Project date of creation
     *
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * Returns a Project date of modification
     *
     * @return mixed
     */
    public function getModifiedAt();
}