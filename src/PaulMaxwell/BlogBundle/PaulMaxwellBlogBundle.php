<?php

namespace PaulMaxwell\BlogBundle;

use PaulMaxwell\BlogBundle\DependencyInjection\Compiler\AppendMainMenuPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PaulMaxwellBlogBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AppendMainMenuPass());
    }
}
