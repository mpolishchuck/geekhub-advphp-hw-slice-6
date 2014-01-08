<?php

namespace PaulMaxwell\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($category_id = false)
    {
        $request = $this->getRequest();

        $after_id = $request->get('after_id');
        $before_id = $request->get('before_id');

        $em = $this->getDoctrine()->getManager();

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $em->getRepository('PaulMaxwellBlogBundle:Article');

        $filter = array();
        $route_settings = array();
        if ($category_id !== false) {
            $filter['category'] = $category_id;
            $route_settings['category_id'] = $category_id;
        }
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\Article[] $articles
         */
        $articles = $ar->findLastArticles(10, $after_id, $before_id, $filter);

        if (count($articles) > 0) {
            $hasPrevious = $ar->hasArticlesBefore($articles[0]->getId(), $filter);
            $hasNext = $ar->hasArticlesAfter($articles[count($articles) - 1]->getId(), $filter);
        } else {
            $hasPrevious = false;
            $hasNext = false;
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('PaulMaxwellBlogBundle:Default:_articles.html.twig', array(
                'articles' => $articles,
                'disableShowMore' => !$hasNext,
                'category' => $category_id,
                'route_settings' => $route_settings,
            ));
        } else {
            return $this->render('PaulMaxwellBlogBundle:Default:index.html.twig', array(
                'articles' => $articles,
                'enableShowPrevious' => (($before_id !== null) || ($after_id !== null)) && $hasPrevious,
                'disableShowMore' => !$hasNext,
                'category' => $category_id,
                'route_settings' => $route_settings,
            ));
        }
    }

    public function showArticleAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $em->getRepository('PaulMaxwellBlogBundle:Article');
        $article = $ar->find($id);

        return $this->render('PaulMaxwellBlogBundle:Default:article.html.twig', array(
            'article' => $article,
        ));
    }

    public function partialSidebarAction()
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $em->getRepository('PaulMaxwellBlogBundle:Article');
        $last = $ar->findLastArticles(5);
        $popular = $ar->findPopularArticles(5);

        return $this->render('PaulMaxwellBlogBundle:Default:_sidebar.html.twig', array(
            'last' => $last,
            'popular' => $popular,
        ));
    }
}
