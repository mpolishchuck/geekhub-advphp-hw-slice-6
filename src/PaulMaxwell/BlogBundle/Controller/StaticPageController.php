<?php

namespace PaulMaxwell\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaticPageController extends Controller
{
    public function indexAction($page)
    {
        /**
         * @var \Knp\Bundle\MarkdownBundle\Parser\MarkdownParser $md
         */
        $md = $this->get('markdown.parser');
        /**
         * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
         */
        $translator = $this->get('translator');

        $locales = array_merge(array($translator->getLocale()), $translator->getFallbackLocales());

        do {
            $file = __DIR__ . '/../Resources/static/' . $page . '.' . $locales[0] . '.md';
            $locales = array_slice($locales, 1);
        } while (!is_file($file) && (count($locales) > 0));

        if (is_file($file)) {
            return $this->render('PaulMaxwellBlogBundle:StaticPage:index.html.twig', array(
                'content' => $md->transformMarkdown(file_get_contents($file)),
            ));
        } else {
            throw new NotFoundHttpException();
        }
    }
}
