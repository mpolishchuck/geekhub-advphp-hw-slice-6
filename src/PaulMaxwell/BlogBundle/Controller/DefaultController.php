<?php

namespace PaulMaxwell\BlogBundle\Controller;

use PaulMaxwell\BlogBundle\Entity\Article;
use PaulMaxwell\BlogBundle\Entity\Category;
use PaulMaxwell\BlogBundle\Entity\Tag;
use PaulMaxwell\BlogBundle\Event\ArticleViewedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var \PaulMaxwell\BlogBundle\Entity\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \PaulMaxwell\BlogBundle\Entity\TagRepository
     */
    protected $tagRepository;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;
    /**
     * @var \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @param \PaulMaxwell\BlogBundle\Entity\ArticleRepository $articleRepository
     */
    public function setArticleRepository($articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param \PaulMaxwell\BlogBundle\Entity\CategoryRepository $categoryRepository
     */
    public function setCategoryRepository($categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \PaulMaxwell\BlogBundle\Entity\TagRepository $tagRepository
     */
    public function setTagRepository($tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs $breadcrumbs
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    public function indexAction(Request $request, $category_id = null, $tag_id = null)
    {
        $after_id = $request->get('after_id');
        $before_id = $request->get('before_id');
        $search = $request->get('search');

        $filter = $this->getFilter($category_id, $tag_id, $search);
        $route_settings = $this->getRouteSettings($category_id, $tag_id, $search);
        if ($category_id !== null) {
            $this->generateBreadcrumbs(
                $this->categoryRepository->find($category_id)
            );
        } elseif ($tag_id !== null) {
            $this->generateBreadcrumbs(
                $this->tagRepository->find($tag_id)
            );
        }
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\Article[] $articles
         */
        $articles = $this->articleRepository->findLastArticles(
            $this->container->getParameter('paul_maxwell_blog.articles_per_page'),
            $after_id,
            $before_id,
            $filter
        );

        if (count($articles) > 0) {
            $this->articleRepository->fetchTagDataByArticles($articles);
            $hasPrevious = $this->articleRepository->hasArticlesBefore($articles[0]->getId(), $filter);
            $hasNext = $this->articleRepository->hasArticlesAfter($articles[count($articles) - 1]->getId(), $filter);
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
         * @var \PaulMaxwell\BlogBundle\Entity\Article $article
         */
        $article = $this->articleRepository->find($id);

        $this->signalArticleViewed($article);
        $this->generateBreadcrumbs($article);

        return $this->render('PaulMaxwellBlogBundle:Default:article.html.twig', array(
            'article' => $article,
        ));
    }

    public function partialSidebarAction()
    {
        $last = $this->articleRepository->findLastArticles(
            $this->container->getParameter('paul_maxwell_blog.articles_per_panel')
        );
        $popular = $this->articleRepository->findPopularArticles(
            $this->container->getParameter('paul_maxwell_blog.articles_per_panel')
        );
        $tags = $this->tagRepository->findAll();
        if (count($tags) > 0) {
            $max_weight = max(array_map(function (Tag $tag) { return $tag->getTimesUsed(); }, $tags));
        } else {
            $max_weight = 1;
        }

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

    /**
     * @param \PaulMaxwell\BlogBundle\Entity\Article|\PaulMaxwell\BlogBundle\Entity\Category|\PaulMaxwell\BlogBundle\Entity\Tag $entity
     */
    protected function generateBreadcrumbs($entity)
    {
        $this->breadcrumbs->addItem(
            'paul_maxwell_blog.main_menu.home',
            $this->router->generate('paul_maxwell_blog_homepage')
        );

        if ($entity instanceof Tag) {
            $this->breadcrumbs->addItem('paul_maxwell_blog.tags');
        }
        $categories = array();
        $category = $this->getEntityParent($entity);
        while ($category) {
            $categories = array_merge(array($category), $categories);
            $category = $category->getParent();
        }

        $router = $this->router;
        $this->breadcrumbs->addObjectArray($categories, 'title', function (Category $category) use ($router) {
            return $router->generate('paul_maxwell_blog_category', array('category_id' => $category->getId()));
        });

        $this->breadcrumbs->addItem($entity->getTitle());
    }

    /**
     * @param \PaulMaxwell\BlogBundle\Entity\Article|\PaulMaxwell\BlogBundle\Entity\Category|\PaulMaxwell\BlogBundle\Entity\Tag $entity
     * @return \PaulMaxwell\BlogBundle\Entity\Category
     */
    protected function getEntityParent($entity)
    {
        if ($entity instanceof Article) {
            return $entity->getCategory();
        } elseif ($entity instanceof Category) {
            return $entity->getParent();
        }

        return null;
    }

    protected function signalArticleViewed(Article $article)
    {
        $event = new ArticleViewedEvent();
        $event->setArticle($article);
        $this->eventDispatcher->dispatch('paul_maxwell_blog_bundle.article_viewed', $event);
    }

    /**
     * @param integer $category_id
     * @param integer $tag_id
     * @param string $search
     * @return array
     */
    protected function getFilter($category_id = null, $tag_id = null, $search = null)
    {
        $filter = array();
        if ($category_id !== false) {
            $filter['category'] = $category_id;
        } elseif ($tag_id !== false) {
            $filter['tag'] = $tag_id;
        }
        if (!empty($search)) {
            $filter['like'] = $search;
        }

        return $filter;
    }

    /**
     * @param integer $category_id
     * @param integer $tag_id
     * @param string $search
     * @return array
     */
    protected function getRouteSettings($category_id = null, $tag_id = null, $search = null)
    {
        $route_settings = array();
        if ($category_id !== false) {
            $route_settings['category_id'] = $category_id;
        } elseif ($tag_id !== false) {
            $route_settings['tag_id'] = $tag_id;
        }
        if (!empty($search)) {
            $route_settings['search'] = $search;
        }

        return $route_settings;
    }
}
