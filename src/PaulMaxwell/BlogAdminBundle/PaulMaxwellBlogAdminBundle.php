<?php

namespace PaulMaxwell\BlogAdminBundle;

use PaulMaxwell\BlogAdminBundle\DependencyInjection\Compiler\TinyMceDocumentBasePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PaulMaxwellBlogAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TinyMceDocumentBasePass());
    }
}
