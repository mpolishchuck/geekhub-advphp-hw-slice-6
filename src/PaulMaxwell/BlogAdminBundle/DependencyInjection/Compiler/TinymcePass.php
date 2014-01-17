<?php

namespace PaulMaxwell\BlogAdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TinymcePass implements CompilerPassInterface
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
        $container->getParameterBag()->set(
            'stfalcon_tinymce.twig.extension.class',
            'PaulMaxwell\BlogAdminBundle\Twig\Extension\PaulMaxwellTinymceExtension'
        );

        $config = $container->getParameterBag()->get('stfalcon_tinymce.config');

        $config['include_jquery'] = false;
        $config['tinymce_jquery'] = true;

        $container->getParameterBag()->set('stfalcon_tinymce.config', $config);
    }
}
