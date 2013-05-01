<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Cmf\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;

class SymfonyCmfMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AddVotersPass());
    }
}
