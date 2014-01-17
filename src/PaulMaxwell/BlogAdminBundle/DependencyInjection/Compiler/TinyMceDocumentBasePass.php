<?php

namespace PaulMaxwell\BlogAdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TinyMceDocumentBasePass implements CompilerPassInterface
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
    }
}
