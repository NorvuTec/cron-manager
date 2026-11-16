<?php

namespace Norvutec\CronManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const DEFAULT_RUN_LOCK_TTL = 300.0;

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('norvutec_cron_manager');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->floatNode('run_lock_ttl')
                    ->info('TTL in seconds for cronmanager:job:<tag> locks. Should exceed the cron-manager:run process timeout (60s). On crash the lock expires after this TTL.')
                    ->defaultValue(self::DEFAULT_RUN_LOCK_TTL)
                    ->min(1.0)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
