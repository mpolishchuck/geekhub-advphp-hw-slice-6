<?php

namespace PaulMaxwell\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PaulMaxwell\BlogBundle\Entity\Category;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

class LoadCategories extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $fixture = $this;

        $categories = $this->getCategories($this->getCategoriesCrawler());

        array_walk($categories, function (&$category) use (&$categories) {
            $cat = new Category();
            $cat->setTitle($category);

            $category = $cat;
        });

        array_walk($categories, function (Category &$category, $path) use ($categories, $fixture, $manager) {
            $parentPath = $fixture->getParentPath($path);
            if (isset ($categories[$parentPath])) {
                $category->setParent($categories[$parentPath]);
            }
            $manager->persist($category);
            $fixture->addReference('cat_' . $path, $category);
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
        return 1;
    }

    /**
     * @param Crawler $crawler
     * @return array
     */
    protected function getCategories(Crawler $crawler)
    {
        $categories = array();

        $crawler->each(function (Crawler $node) use (&$categories) {
            $categories[html_entity_decode(
                $node->filter('path')->text(),
                ENT_NOQUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            )] = html_entity_decode(
                $node->filter('title')->text(),
                ENT_NOQUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        });

        return $categories;
    }

    /**
     * @param string $path
     * @return null|string
     */
    protected function getParentPath($path)
    {
        if (($pos = strrpos($path, '/')) !== false) {
            return substr($path, 0, $pos);
        }

        return null;
    }

    /**
     * @return Crawler
     */
    protected function getCategoriesCrawler()
    {
        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents(__DIR__ . '/data/j2xml1250020140108104918.xml'));
        CssSelector::disableHtmlExtension();

        return $crawler->filter('j2xml category');
    }
}
