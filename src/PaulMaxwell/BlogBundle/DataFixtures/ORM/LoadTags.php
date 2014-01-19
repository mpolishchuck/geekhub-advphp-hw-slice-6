<?php

namespace PaulMaxwell\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PaulMaxwell\BlogBundle\Entity\Article;
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
        $tags = $this->getTagData();
        array_walk($tags, function (&$tag) {
            $t = new Tag();
            $t->setTitle($tag);

            $tag = $t;
        });

        /**
         * @var \PaulMaxwell\BlogBundle\Entity\ArticleRepository $ar
         */
        $ar = $manager->getRepository('PaulMaxwellBlogBundle:Article');

        $this->bindTags($ar->findAll(), $tags);
        $this->persistEntities($tags, $manager);

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

    protected function getTagData()
    {
        return Yaml::parse(__DIR__ . '/data/tags.yml');
    }

    protected function persistEntities($tags, ObjectManager $manager)
    {
        array_walk($tags, function (Tag &$tag) use ($manager) {
            $manager->persist($tag);
        });
    }

    protected function bindTags($articles, $tags)
    {
        array_walk($articles, function (Article &$article) use ($tags) {
            $tagCount = mt_rand(0, round(count($tags) * 0.8));
            $keys = array_keys($tags);
            shuffle($keys);
            $keys = array_slice($keys, 0, $tagCount);
            array_walk($keys, function (&$key) use ($article, $tags) {
                $article->addTag($tags[$key]);
            });
        });
    }
}
