<?php

namespace PaulMaxwell\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StaticPageController extends Controller
{
    /**
     * @var \Knp\Bundle\MarkdownBundle\Parser\MarkdownParser
     */
    protected $markdownParser;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * @param \Knp\Bundle\MarkdownBundle\Parser\MarkdownParser $markdownParser
     */
    public function setMarkdownParser($markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function indexAction($page)
    {
        $locales = array_merge(
            array($this->translator->getLocale()),
            $this->translator->getFallbackLocales()
        );

        foreach ($locales as $locale) {
            $file = __DIR__ . '/../Resources/static/' . $page . '.' . $locale . '.md';
            if (is_file($file)) {
                return $this->render('PaulMaxwellBlogBundle:StaticPage:index.html.twig', array(
                    'content' => $this->markdownParser->transformMarkdown(file_get_contents($file)),
                ));
            }
        }

        throw $this->createNotFoundException();
    }
}
