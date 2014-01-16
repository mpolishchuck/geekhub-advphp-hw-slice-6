<?php

namespace PaulMaxwell\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class ArticleRepository extends EntityRepository
{
    public function findLastArticles($limit, $after_id = null, $before_id = null, $filter = array())
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->addSelect('c')
            ->leftJoin('a.category', 'c')
            ->orderBy('a.postedAt', ($before_id !== null) ? 'ASC' : 'DESC')
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
            /**
             * @var Article $article
             */
            $article = $this->find($after_id);

            $queryBuilder->andWhere('a.postedAt < :after_posted_at')
                ->setParameter(':after_posted_at', $article->getPostedAt());
        } elseif ($before_id !== null) {
            /**
             * @var Article $article
             */
            $article = $this->find($before_id);

            $queryBuilder->andWhere('a.postedAt > :before_posted_at')
                ->setParameter(':before_posted_at', $article->getPostedAt());
        }

        $result = $queryBuilder->getQuery()->getResult();

        return ($before_id !== null) ? array_reverse($result) : $result;
    }

    public function findPopularArticles($limit)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->orderBy('a.hits', 'DESC')
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param integer $id
     * @param array $filter
     * @return bool
     */
    public function hasArticlesBefore($id, $filter = array())
    {
        /**
         * @var Article $article
         */
        $article = $this->find($id);

        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.postedAt > :before_posted_at')
            ->setParameter(':before_posted_at', $article->getPostedAt());

        return $this->checkOneArticleExistence($queryBuilder, $filter);
    }

    /**
     * @param integer $id
     * @param array $filter
     * @return bool
     */
    public function hasArticlesAfter($id, $filter = array())
    {
        /**
         * @var Article $article
         */
        $article = $this->find($id);

        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.postedAt < :after_posted_at')
            ->setParameter(':after_posted_at', $article->getPostedAt());

        return $this->checkOneArticleExistence($queryBuilder, $filter);
    }

    /**
     * @param integer $id
     */
    public function increaseHitsById($id)
    {
        $query = $this
            ->getEntityManager()
            ->createQuery('UPDATE PaulMaxwellBlogBundle:Article a SET a.hits = a.hits + 1 WHERE a.id = :article_id')
            ->setParameter(':article_id', $id);
        $query->execute();
    }

    public function fetchTagDataByArticles($articles)
    {
        $this->createQueryBuilder('a')
            ->addSelect('t')
            ->leftJoin('a.tags', 't')
            ->where('a.id IN (:ids)')
            ->setParameter(
                ':ids',
                array_map(function (Article $article) { return $article->getId(); }, $articles)
            )
            ->getQuery()->execute();
    }

    protected function checkOneArticleExistence(QueryBuilder $queryBuilder, $filter = array())
    {
        $queryBuilder->orderBy('a.postedAt', 'ASC')
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
