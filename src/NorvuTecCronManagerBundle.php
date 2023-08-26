<?php

namespace NorvuTec\CronManagerBundle;

use NorvuTec\CronManagerBundle\DependencyInjection\NorvuTecCronManagerExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class NorvuTecCronManagerBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new NorvuTecCronManagerExtension();
    }


}