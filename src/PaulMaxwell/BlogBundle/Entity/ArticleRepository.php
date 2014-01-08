<?php

namespace PaulMaxwell\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public function findLastArticles($limit, $category_id = null)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->orderBy('a.postedAt', 'DESC')
            ->setMaxResults($limit);
        if ($category_id !== null) {
            $queryBuilder->where('a.category = :category_id')
                ->setParameter(':category_id', $category_id);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findPopularArticles($limit)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->orderBy('a.hits', 'DESC')
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }
}
