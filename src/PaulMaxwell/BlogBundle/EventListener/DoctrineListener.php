<?php

namespace PaulMaxwell\BlogBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PaulMaxwell\BlogBundle\Entity\Tag;

class DoctrineListener
{
    public function prePersist(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof Tag) {
            /**
             * @var \PaulMaxwell\BlogBundle\Entity\Tag $tag
             */
            $tag = $event->getEntity();
            $tag->setTimesUsed(count($tag->getArticles()));
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($event->getEntity() instanceof Tag) {
            /**
             * @var \PaulMaxwell\BlogBundle\Entity\Tag $tag
             */
            $tag = $event->getEntity();
            $tag->setTimesUsed(count($tag->getArticles()));

            $uow->recomputeSingleEntityChangeSet(
                $em->getClassMetadata(get_class($tag)),
                $tag
            );
        }
    }
}
