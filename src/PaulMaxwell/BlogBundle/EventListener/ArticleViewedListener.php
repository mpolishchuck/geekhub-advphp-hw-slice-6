<?php

namespace PaulMaxwell\BlogBundle\EventListener;

use PaulMaxwell\BlogBundle\Entity\ArticleRepository;
use PaulMaxwell\BlogBundle\Event\ArticleViewedEvent;

class ArticleViewedListener
{
    /**
     * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository
     */
    protected $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onArticleViewed(ArticleViewedEvent $event)
    {
        $this->repository->increaseHitsById($event->getArticle()->getId());
    }
}
