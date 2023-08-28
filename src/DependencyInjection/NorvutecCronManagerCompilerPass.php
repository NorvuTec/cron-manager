<?php

namespace Norvutec\CronManagerBundle\DependencyInjection;

use Norvutec\CronManagerBundle\Service\CronManagerService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NorvutecCronManagerCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container)
    {
        if(!$container->has(CronManagerService::class)) {
            return;
        }
        $definition = $container->findDefinition(CronManagerService::class);
        $taggedServices = $container->findTaggedServiceIds('norvutec.cron_manager_bundle.cronjob');
        foreach($taggedServices as $id => $tags) {
            $definition->addMethodCall('addCronjobService', [new Reference($id), $tags]);
        }
    }


}