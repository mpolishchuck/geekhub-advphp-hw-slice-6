<?php

namespace PaulMaxwell\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $em->getRepository('PaulMaxwellBlogBundle:Article');
        $articles = $ar->findLastArticles(10);

        return $this->render('PaulMaxwellBlogBundle:Default:index.html.twig', array(
            'articles' => $articles,
        ));
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

    public function showCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $em->getRepository('PaulMaxwellBlogBundle:Article');
        $articles = $ar->findLastArticles(10, $id);

        return $this->render('PaulMaxwellBlogBundle:Default:index.html.twig', array(
            'articles' => $articles,
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
