<?php

declare(strict_types=1);
namespace Linderp\SuluIndexNowBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SuluIndexNowExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter(
            'sulu_index_now.key',
            $config['key']
        );
        $container->setParameter(
            'sulu_index_now.search_engines',
            $config['search_engines']
        );
        $loader->load('services.yaml');

    }
}
