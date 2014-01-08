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
        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents(__DIR__ . '/data/j2xml1250020140107183129.xml'));

        CssSelector::disableHtmlExtension();
        $content = $crawler->filter('j2xml content');

        $fixture = $this;

        $content->each(function (Crawler $node, $i) use ($fixture, $manager) {
            if ($node->filter('state')->text() != '1') {
                // Skip unpublished articles
                return;
            }

            $catid = html_entity_decode(
                $node->filter('catid')->text(),
                ENT_NOQUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
            if (!$fixture->hasReference('cat_' . $catid))
                return;
            $article = new Article();
            $article->setTitle(html_entity_decode(
                $node->filter('title')->text(),
                ENT_NOQUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ));
            $article->setBody(html_entity_decode(
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
            $article->setCategory($fixture->getReference('cat_' . $catid));

            $manager->persist($article);
        });

        $manager->flush();

        $images = $crawler->filter('j2xml img');
        $images->each(function (Crawler $node, $i) use ($fixture) {
            $filename = $node->attr('src');
            $content = trim($node->text());

            if (is_file($fixture->container->getParameter('paul_maxwell_blog.images_location') . '/' . $filename)) {
                return;
            }

            if (!is_dir($fixture->container->getParameter('paul_maxwell_blog.images_location') . '/' . dirname($filename))) {
                mkdir(
                    $fixture->container->getParameter('paul_maxwell_blog.images_location') . '/' . dirname($filename),
                    0777,
                    true
                );
            }

            file_put_contents(
                $fixture->container->getParameter('paul_maxwell_blog.images_location') . '/' . $filename,
                base64_decode($content)
            );
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
}
