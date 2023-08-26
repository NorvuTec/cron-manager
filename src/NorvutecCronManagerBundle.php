<?php

namespace Norvutec\CronManagerBundle;

use Norvutec\CronManagerBundle\Attribute\Cronjob;
use Norvutec\CronManagerBundle\DependencyInjection\NorvutecCronManagerCompilerPass;
use Norvutec\CronManagerBundle\DependencyInjection\NorvutecCronManagerExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class NorvutecCronManagerBundle extends AbstractBundle
{
    public function getPath(): string {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface {
        return new NorvutecCronManagerExtension();
    }

    public function build(ContainerBuilder $container): void {
        $container->registerAttributeForAutoconfiguration(
            Cronjob::class,
            static function (ChildDefinition $definition, Cronjob $cronjob, \ReflectionClass $reflector): void {
                $definition->addTag('norvutec.cron_manager_bundle.cronjob', [
                    "name" => $cronjob->getName(),
                    "class" => $reflector->getName()
                ]);
            }
        );
        $container->addCompilerPass(new NorvutecCronManagerCompilerPass());
    }

}