<?php

/*
 * This file is part of the Trinity project.
 */

namespace Necktie\NewsletterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Class DriverCompilerPass.
 */
class DriverCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('necktie.newsletter')) {
            return;
        }

        $definition = $container->getDefinition('necktie.newsletter');

        foreach ($container->findTaggedServiceIds('necktie.newsletter.driver') as $serviceId => $key) {
            $definition->addMethodCall('addDriver', [new Reference($serviceId)]);
        }
    }
}
