<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="user", cascade={"remove"})
     */
    protected $projects;


    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addProject(Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    public function removeProject(Project $project)
    {
        $this->projects->removeElement($project);
    }

    public function getProjects()
    {
        return $this->projects;
    }
}
