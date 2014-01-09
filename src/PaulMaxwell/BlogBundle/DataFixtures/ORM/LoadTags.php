<?php

namespace PaulMaxwell\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PaulMaxwell\BlogBundle\Entity\Tag;
use Symfony\Component\Yaml\Yaml;

class LoadTags extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $tagData = Yaml::parse(__DIR__ . '/data/tags.yml');

        foreach ($tagData as $tagTitle) {
            $tag = new Tag();
            $tag->setTitle($tagTitle);

            $manager->persist($tag);
        }

        $manager->flush();

        // Clear cached objects
        $manager->clear();

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $manager->getRepository('PaulMaxwellBlogBundle:Article');
        $articles = $ar->findAll();
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\TagRepository $tr
         */
        $tr = $manager->getRepository('PaulMaxwellBlogBundle:Tag');
        /**
         * @var \PaulMaxwell\BlogBundle\Entity\Tag[] $tags
         */
        $tags = $tr->findAll();

        foreach ($articles as $article) {
            /**
             * @var \PaulMaxwell\BlogBundle\Entity\Article $article
             */
            $tagCount = mt_rand(0, round(count($tags) * 0.8));
            $keys = array_keys($tags);
            shuffle($keys);
            $keys = array_slice($keys, 0, $tagCount);
            foreach ($keys as $key) {
                $article->getTags()->add($tags[$key]);
                $tags[$key]->getArticles()->add($article);
            }
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
        return 3;
    }
}
