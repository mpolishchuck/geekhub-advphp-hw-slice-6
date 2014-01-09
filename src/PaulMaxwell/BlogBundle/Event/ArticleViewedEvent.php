<?php

namespace PaulMaxwell\BlogBundle\Event;

use PaulMaxwell\BlogBundle\Entity\Article;
use Symfony\Component\EventDispatcher\Event;

class ArticleViewedEvent extends Event
{
    protected $article;

    /**
     * @param Article $article
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }
}
