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
        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents(__DIR__ . '/data/j2xml1250020140108104918.xml'));

        CssSelector::disableHtmlExtension();
        $crawler = $crawler->filter('j2xml category');

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

        $insertedCategories = array();
        ksort($categories);

        foreach ($categories as $path => $title) {
            $category = new Category();
            $category->setTitle($title);
            if (($pos = strrpos($path, '/')) !== false) {
                $parentPath = substr($path, 0, $pos);
                if (isset($insertedCategories[$parentPath])) {
                    $category->setParent($insertedCategories[$parentPath]);
                }
            }

            $manager->persist($category);

            $insertedCategories[$path] = $category;
            $this->addReference('cat_' . $path, $category);
        }

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
}
