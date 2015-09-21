<?php

namespace Ku\SsoClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KuSsoClientExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->findDefinition('ku_sso_client.security.entry_point')
            ->replaceArgument(2, $config['sso']['login'])
            ->replaceArgument(3, $config['server_login_url']);

        $container->findDefinition('ku_sso_client.security.authentication.guzzle_client')
            ->replaceArgument(0, array(
                'base_url' => $config['sso']['authentication']
            ));

        $container->findDefinition('ku_sso_client.security.decrypter')
            ->replaceArgument(0, $config['api_key']);
    }
}
