<?php

namespace PaulMaxwell\BlogBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use PaulMaxwell\BlogBundle\Event\ArticleViewedEvent;

class ArticleViewedListener
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onArticleViewed(ArticleViewedEvent $event)
    {
        $em = $this->doctrine->getManager();
        /**
         * @var \Doctrine\ORM\Query $query
         */
        $query = $em
            ->createQuery('UPDATE PaulMaxwellBlogBundle:Article a SET a.hits = a.hits + 1 WHERE a.id = :article_id')
            ->setParameter(':article_id', $event->getArticle()->getId());
        $query->execute();
    }
}
