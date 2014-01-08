<?php

namespace PaulMaxwell\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

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
        if (isset($filter['tag']) && ($filter['tag'] !== false)) {
            $queryBuilder->innerJoin('a.tags', 't', Join::WITH, 't.id = :tag_id')
                ->setParameter(':tag_id', $filter['tag']);
        }
        if (isset($filter['like']) && !empty($filter['like'])) {
            $queryBuilder->andWhere('(a.title LIKE :text_search OR a.body LIKE :text_search)')
                ->setParameter(':text_search', '%' . $filter['like'] . '%');
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
        if (isset($filter['tag']) && ($filter['tag'] !== false)) {
            $queryBuilder->innerJoin('a.tags', 't', Join::WITH, 't.id = :tag_id')
                ->setParameter(':tag_id', $filter['tag']);
        }
        if (isset($filter['like']) && !empty($filter['like'])) {
            $queryBuilder->andWhere('(a.title LIKE :text_search OR a.body LIKE :text_search)')
                ->setParameter(':text_search', '%' . $filter['like'] . '%');
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
        if (isset($filter['tag']) && ($filter['tag'] !== false)) {
            $queryBuilder->innerJoin('a.tags', 't', Join::WITH, 't.id = :tag_id')
                ->setParameter(':tag_id', $filter['tag']);
        }
        if (isset($filter['like']) && !empty($filter['like'])) {
            $queryBuilder->andWhere('(a.title LIKE :text_search OR a.body LIKE :text_search)')
                ->setParameter(':text_search', '%' . $filter['like'] . '%');
        }

        return (count($queryBuilder->getQuery()->getResult()) > 0);
    }
}
