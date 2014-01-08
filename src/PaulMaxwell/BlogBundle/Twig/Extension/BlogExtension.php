<?php

namespace PaulMaxwell\BlogBundle\Twig\Extension;

use Symfony\Component\DomCrawler\Crawler;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class BlogExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Bundle\TwigBundle\Extension\AssetsExtension
     */
    private $assetsExtension;

    public function __construct($assetsExtension)
    {
        $this->assetsExtension = $assetsExtension;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'first_paragraph',
                array($this, 'firstParagraphFilter'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFilter(
                'fix_img_tags',
                array($this, 'fixImgTagsFilter'),
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFilter(
                'tag_weight_percent',
                array($this, 'tagWeightPercentFilter')
            ),
        );
    }

    public function firstParagraphFilter($input)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($input);
        $crawler = $crawler->filter('p');

        return '<p>'.$crawler->html().'</p>';
    }

    public function fixImgTagsFilter($input)
    {
        $crawler = new HtmlPageCrawler($input);
        $this->fixImgTagsInCrawler($crawler);

        return $crawler->saveHTML();
    }

    public function tagWeightPercentFilter($input, $max_weight)
    {
        $max_weight = $max_weight ?: 1;
        return round(50 + 150 * ($input / $max_weight));
    }

    public function getName()
    {
        return 'blog_extension';
    }

    protected function fixImgTagsInCrawler(HtmlPageCrawler $crawler)
    {
        $extension = $this;

        $crawler->each(function (HtmlPageCrawler $node, $i) use ($extension) {
            if ($node->nodeName() == 'img') {
                $node->setAttribute('src', $extension->assetsExtension->getAssetUrl($node->attr('src')));
            } else {
                $extension->fixImgTagsInCrawler($node->children());
            }
        });
    }
}
