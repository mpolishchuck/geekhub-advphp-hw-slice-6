<?php

namespace PaulMaxwell\BlogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppendMainMenuPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $mainMenuAppend = $container->getParameterBag()->get('paul_maxwell_blog.main_menu_append');

        $serviceConfig = $container->getDefinition('paul_maxwell_blog_bundle.twig.main_menu_extension');
        foreach ($mainMenuAppend as $item) {
            $serviceConfig->addMethodCall('append', array($item));
        }
    }
}
