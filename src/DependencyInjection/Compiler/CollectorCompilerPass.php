<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function count;
use function is_array;

/**
 * Adds tagged data_collector services to profiler service
 * strongly inspired by Symfony ProfilerPass.
 *
 * @author <liuggio@gmail.com>
 */
class CollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('liuggio_stats_d_client.collector.service')) {
            return;
        }

        $serviceDefinition = $container->getDefinition('liuggio_stats_d_client.collector.service');
        $collectorsEnabled = $container->getParameter('liuggio_stats_d_client.collectors');
        $collectorIsEnabled = $container->getParameter('liuggio_stats_d_client.enable_collector');

        if ($collectorIsEnabled && null !== $collectorsEnabled && is_array($collectorsEnabled) && count($collectorsEnabled) > 0) {
            foreach ($container->findTaggedServiceIds('stats_d_collector') as $id => $attributes) {
                // only if there's on the parameter means that is enable
                if (isset($collectorsEnabled[$id])) {
                    // setterInjection
                    $collectorReference = new Reference($id);
                    $collectorDefinition = $container->getDefinition($id);
                    $collectorDefinition->addMethodCall('setStatsdDataFactory', [new Reference('liuggio_stats_d_client.factory')]);
                    $collectorDefinition->addMethodCall('setStatsDataKey', [$collectorsEnabled[$id]]);
                    // adding to this collector the the collection
                    $serviceDefinition->addMethodCall('add', [$collectorReference]);
                    // if is doctrine.dbal
                    // we need to attach also the collector to the dbal sql logger chain
                    if ('liuggio_stats_d_client.collector.dbal' === $id) {
                        $chainLogger = $container->getDefinition('doctrine.dbal.logger.chain');
                        if (null !== $chainLogger) {
                            $chainLogger->addMethodCall('addLogger', [$collectorReference]);
                        }
                    }
                }
            }
        }
    }
}
