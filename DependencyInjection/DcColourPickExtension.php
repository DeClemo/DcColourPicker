<?php
/**
 * 
 * Author: Daniel Clements
 * Date: 13/05/15
 * Time: 11:45 AM
 */

namespace DanielClements\ColourPickerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DcColourPickExtension extends Extension implements PrependExtensionInterface
{
    protected function getFilesToPrepend()
    {
        return array(
            array(
                'config_file' => 'ezpublish_field_templates.yml',
                'name' => 'ezpublish'
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $configSettings = $this->getFilesToPrepend();

        foreach ($configSettings as $file)
        {
            $configFile = __DIR__ . '/../Resources/config/'.$file['config_file'];
            $config = Yaml::parse( file_get_contents( $configFile ) );
            $container->prependExtensionConfig( $file['name'], $config );
            $container->addResource( new FileResource( $configFile ) );
        }
    }
}