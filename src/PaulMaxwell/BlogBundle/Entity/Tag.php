<?php

namespace PaulMaxwell\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tag
 * @package PaulMaxwell\BlogBundle\Entity
 *
 * @ORM\Entity(repositoryClass="PaulMaxwell\BlogBundle\Entity\TagRepository")
 * @ORM\Table(name="tag")
 */
class Tag
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(name="times_used", type="integer")
     */
    protected $timesUsed;

    /**
     * @ORM\ManyToMany(targetEntity="Article", inversedBy="tags")
     */
    protected $articles;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $timesUsed
     */
    public function setTimesUsed($timesUsed)
    {
        $this->timesUsed = $timesUsed;
    }

    /**
     * @return integer
     */
    public function getTimesUsed()
    {
        return $this->timesUsed;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }
}
