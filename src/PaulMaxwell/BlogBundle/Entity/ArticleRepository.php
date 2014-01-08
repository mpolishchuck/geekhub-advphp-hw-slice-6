<?php

namespace PaulMaxwell\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public function findLastArticles($limit, $after_id = null, $before_id = null, $filter = array())
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->orderBy('a.postedAt', 'DESC')
            ->setMaxResults($limit);
        if (isset($filter['category']) && ($filter['category'] !==  false)) {
            $queryBuilder->andWhere('a.category = :category_id')
                ->setParameter(':category_id', $filter['category']);
        }
        if ($after_id !== null) {
            $queryBuilder->andWhere('a.id < :after_id')
                ->setParameter(':after_id', $after_id);
        } elseif ($before_id !== null) {
            $queryBuilder->andWhere('a.id > :before_id')
                ->setParameter(':before_id', $before_id);
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

    public function hasArticlesBefore($id, $filter = array())
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.id > :id')
            ->setParameter('id', $id)
            ->orderBy('a.postedAt', 'ASC')
            ->addOrderBy('a.id', 'ASC')
            ->setMaxResults(1);
        if (isset($filter['category']) && ($filter['category'] !==  false)) {
            $queryBuilder->andWhere('a.category = :category_id')
                ->setParameter(':category_id', $filter['category']);
        }

        return (count($queryBuilder->getQuery()->getResult()) > 0);
    }

    public function hasArticlesAfter($id, $filter = array())
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.id < :id')
            ->setParameter('id', $id)
            ->orderBy('a.postedAt', 'ASC')
            ->addOrderBy('a.id', 'ASC')
            ->setMaxResults(1);
        if (isset($filter['category']) && ($filter['category'] !==  false)) {
            $queryBuilder->andWhere('a.category = :category_id')
                ->setParameter(':category_id', $filter['category']);
        }

        return (count($queryBuilder->getQuery()->getResult()) > 0);
    }
}
