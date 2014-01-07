<?php

namespace PaulMaxwell\BlogBundle\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MainMenuExtension extends \Twig_Extension
{
    private $router;
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;
    private $translator;
    private $prependedItems = array();
    private $appendedItems = array();

    public function __construct(ContainerInterface $container, Router $router, Translator $translator)
    {
        $this->router = $router;
        $this->request = $container->get('request');
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('main_menu', array($this, 'getMainMenu'), array('is_safe' => array('html'))),
        );
    }

    public function getMainMenu()
    {
        $items = array_merge($this->prependedItems, func_get_args(), $this->appendedItems);
        $output = '';

        foreach ($items as $item) {
            $output .= '<li';
            if ($this->request->attributes->get('_route') == $item['route']) {
                $output .= ' class="active"';
            }
            $output .= '><a href="';
            $output .= htmlspecialchars($this->router->generate(
                $item['route'],
                isset($item['parameters']) ? $item['parameters'] : array()
            ));
            $output .= '">';
            $output .= $this->translator->trans($item['text']);
            $output .= '</a></li>';
        }

        return $output;
    }

    public function prepend($item)
    {
        $this->prependedItems = array_merge(array($item), $this->prependedItems);
    }

    public function append($item)
    {
        $this->appendedItems = array_merge($this->appendedItems, array($item));
    }

    public function getName()
    {
        return 'main_menu_extension';
    }
}
