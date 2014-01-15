<?php

namespace PaulMaxwell\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Article
 * @package PaulMaxwell\BlogBundle\Entity
 *
 * @ORM\Entity(repositoryClass="PaulMaxwell\BlogBundle\Entity\ArticleRepository")
 * @ORM\Table(name="article")
 */
class Article
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="articles")
     */
    protected $category;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * @ORM\Column(name="posted_at", type="datetime")
     */
    protected $postedAt;

    /**
     * @ORM\Column(name="modified_at", type="datetime")
     */
    protected $modifiedAt;

    /**
     * @ORM\Column(type="integer")
     */
    protected $hits;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="articles")
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param integer $hits
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
    }

    /**
     * @return integer
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $postedAt
     */
    public function setPostedAt($postedAt)
    {
        $this->postedAt = $postedAt;
    }

    /**
     * @return \DateTime
     */
    public function getPostedAt()
    {
        return $this->postedAt;
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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
