<?php

namespace Necktie\NewsletterBundle;

use Necktie\NewsletterBundle\DependencyInjection\DriverCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class NecktieNewsletterBundle.
 */
class NecktieNewsletterBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DriverCompilerPass());
    }

}
