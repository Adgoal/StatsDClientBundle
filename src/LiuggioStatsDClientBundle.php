<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle;

use Liuggio\StatsDClientBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class LiuggioStatsDClientBundle
 * @package Liuggio\StatsDClientBundle
 */
class LiuggioStatsDClientBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CollectorCompilerPass());
    }

}
