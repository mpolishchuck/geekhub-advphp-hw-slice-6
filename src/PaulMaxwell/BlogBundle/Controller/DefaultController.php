<?php

namespace PaulMaxwell\BlogBundle\Controller;

use PaulMaxwell\BlogBundle\Entity\Tag;
use PaulMaxwell\BlogBundle\Event\ArticleViewedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($category_id = false, $tag_id = false)
    {
        $request = $this->getRequest();

        $after_id = $request->get('after_id');
        $before_id = $request->get('before_id');
        $search = $request->get('search');

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $this->get('paul_maxwell_blog_bundle.repository.article');

        $filter = array();
        $route_settings = array();
        if ($category_id !== false) {
            $filter['category'] = $category_id;
            $route_settings['category_id'] = $category_id;
        } elseif ($tag_id !== false) {
            $filter['tag'] = $tag_id;
            $route_settings['tag_id'] = $tag_id;
        }
        if (!empty($search)) {
            $filter['like'] = $search;
            $route_settings['search'] = $search;
        }
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\Article[] $articles
         */
        $articles = $ar->findLastArticles(
            $this->container->getParameter('paul_maxwell_blog.articles_per_page'),
            $after_id,
            $before_id,
            $filter
        );

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
                'search_term' => $search,
            ));
        }
    }

    public function showArticleAction($id)
    {
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $this->get('paul_maxwell_blog_bundle.repository.article');
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\Article $article
         */
        $article = $ar->find($id);

        $event = new ArticleViewedEvent();
        $event->setArticle($article);
        /**
         * @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
         */
        $eventDispatcher = $this->get('event_dispatcher');
        $eventDispatcher->dispatch('paul_maxwell_blog_bundle.article_viewed', $event);

        return $this->render('PaulMaxwellBlogBundle:Default:article.html.twig', array(
            'article' => $article,
        ));
    }

    public function partialSidebarAction()
    {
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $this->get('paul_maxwell_blog_bundle.repository.article');
        $last = $ar->findLastArticles(
            $this->container->getParameter('paul_maxwell_blog.articles_per_panel')
        );
        $popular = $ar->findPopularArticles(
            $this->container->getParameter('paul_maxwell_blog.articles_per_panel')
        );
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\TagRepository $tr
         */
        $tr = $this->get('paul_maxwell_blog_bundle.repository.tag');
        $tags = $tr->findAll();
        $max_weight = max(array_map(function (Tag $tag) { return $tag->getTimesUsed(); }, $tags));
        /**
         * @var \PaulMaxwell\GuestbookBundle\Entity\MessageRepository $mr
         */
        $mr = $this->getDoctrine()->getManager()->getRepository('PaulMaxwellGuestbookBundle:Message');
        $gb_posts = $mr->findFirstSlice(
            $this->container->getParameter('paul_maxwell_blog.gb_posts_per_panel')
        );

        return $this->render('PaulMaxwellBlogBundle:Default:_sidebar.html.twig', array(
            'last' => $last,
            'popular' => $popular,
            'tags' => $tags,
            'tag_max_weight' => $max_weight,
            'gb_posts' => $gb_posts,
        ));
    }
}
