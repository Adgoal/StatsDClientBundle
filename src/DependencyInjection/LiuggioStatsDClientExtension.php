<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use function constant;
use function count;
use function is_int;
use function strtoupper;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link https://symfony.com/doc/current/bundles/extension.html}
 */
class LiuggioStatsDClientExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws Exception
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias().'.sender.class', $config['connection']['class']);
        $container->setParameter($this->getAlias().'.sender.debug_class', $config['connection']['debug_class']);

        foreach ($config['connection'] as $k => $v) {
            $container->setParameter($this->getAlias().'.connection.'.$k, $v);
        }
        $container->setParameter($this->getAlias().'.enable_collector', $config['enable_collector']);
        $container->setParameter($this->getAlias().'.collectors', $config['collectors']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if ($config['enable_collector']) {
            $loader->load('collectors.yml');

            if (count($config['collectors'])) {
                // Define the Listener
                $definition = new Definition('%liuggio_stats_d_client.collector.listener.class%',
                    [new Reference('liuggio_stats_d_client.collector.service')]
                );
                $definition->addTag('kernel.event_subscriber');
                $container->setDefinition('liuggio_stats_d_client.collector.listener', $definition);
            }
        }
        // monolog
        if (!empty($config['monolog']) && $config['monolog']['enable']) {
            $this->loadMonologHandler($config, $container);
        }
        // set the debug sender
        if ($config['connection']['debug']) {
            $senderService = new Definition('%liuggio_stats_d_client.sender.debug_class%');
            $container->setDefinition('liuggio_stats_d_client.sender.service', $senderService);
            $senderService->setArguments([]);
        }
    }

    /**
     * @param $level
     * @return int|mixed
     */
    private function convertLevelToConstant($level)
    {
        return is_int($level) ? $level : constant('Monolog\Logger::'. strtoupper($level));
    }

    /**
     * Loads the Monolog configuration.
     *
     * @param array            $config    A configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function loadMonologHandler(array $config, ContainerBuilder $container)
    {
        $def2 = new Definition($config['monolog']['formatter']['class'], [
                $config['monolog']['formatter']['format'],
                $config['monolog']['formatter']['context_logging'],
                $config['monolog']['formatter']['extra_logging'],
                $config['monolog']['formatter']['words'],
            ]
        );
        $container->setDefinition('monolog.formatter.statsd', $def2);

        $def = new Definition($container->getParameter('liuggio_stats_d_client.monolog_handler.class'), [
            new Reference('liuggio_stats_d_client.service'),
            new Reference('liuggio_stats_d_client.factory'),
            $config['monolog']['prefix'],
            $this->convertLevelToConstant($config['monolog']['level']),
        ]);
        $def->setPublic(false);
        $def->addMethodCall('setFormatter', [new Reference('monolog.formatter.statsd')]);

        $container->setDefinition('monolog.handler.statsd', $def);
    }
}
