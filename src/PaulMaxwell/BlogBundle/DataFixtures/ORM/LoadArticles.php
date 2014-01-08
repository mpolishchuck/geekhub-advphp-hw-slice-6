<?php

namespace PaulMaxwell\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PaulMaxwell\BlogBundle\Entity\Article;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

class LoadArticles extends AbstractFixture implements OrderedFixtureInterface
{

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
        $crawler = $crawler->filter('j2xml content');

        $fixture = $this;

        $crawler->each(function (Crawler $node, $i) use ($fixture, $manager) {
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
}
