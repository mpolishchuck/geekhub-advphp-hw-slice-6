<?php

namespace PaulMaxwell\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PaulMaxwell\BlogBundle\Entity\Article;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

class LoadArticles extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $fixture = $this;

        $this->getContentCrawler()->each(function (Crawler $node) use ($fixture, $manager) {
            $fixture->processContentNode($node, $manager);
        });

        $manager->flush();

        $images = $this->getImagesCrawler();
        $images->each(function (Crawler $node) use ($fixture) {
            $fixture->processImageNode($node);
        });
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 2;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return Crawler
     */
    protected function getDataCrawler()
    {
        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents(__DIR__ . '/data/j2xml1250020140107183129.xml'));
        CssSelector::disableHtmlExtension();

        return $crawler;
    }

    /**
     * @return Crawler
     */
    protected function getContentCrawler()
    {
        return $this->getDataCrawler()->filter('j2xml content');
    }

    /**
     * @return Crawler
     */
    protected function getImagesCrawler()
    {
        return $this->getDataCrawler()->filter('j2xml img');
    }

    /**
     * @param Crawler $node
     * @param ObjectManager $manager
     */
    protected function processContentNode(Crawler $node, ObjectManager $manager)
    {
        if ($node->filter('state')->text() != '1') {
            // Skip unpublished articles
            return;
        }

        $categoryId = html_entity_decode(
            $node->filter('catid')->text(),
            ENT_NOQUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
        if (!$this->hasReference('cat_' . $categoryId)) {
            return;
        }

        $article = new Article();
        $article->setTitle(html_entity_decode(
            $node->filter('title')->text(),
            ENT_NOQUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        ));
        $article->setBody(html_entity_decode(
                $node->filter('introtext')->text(),
                ENT_NOQUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ) . html_entity_decode(
                $node->filter('fulltext')->text(),
                ENT_NOQUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ));
        $article->setHits(html_entity_decode(
            $node->filter('hits')->text(),
            ENT_NOQUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        ));
        $article->setPostedAt(new \DateTime(html_entity_decode(
            $node->filter('created')->text(),
            ENT_NOQUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        )));
        $article->setModifiedAt(new \DateTime(html_entity_decode(
            $node->filter('modified')->text(),
            ENT_NOQUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        )));
        $article->setCategory($this->getReference('cat_' . $categoryId));

        $manager->persist($article);
    }

    /**
     * @param Crawler $node
     */
    protected function processImageNode(Crawler $node)
    {
        $location = $this->container->getParameter('paul_maxwell_blog.images_location');
        $filename = $location . '/' . $node->attr('src');
        $content = trim($node->text());

        if (is_file($filename)) {
            return;
        }

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        file_put_contents($filename, base64_decode($content));
    }
}
