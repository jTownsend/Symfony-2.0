<?php

namespace Bundle\Tecbot\AssetPackagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AssetPackagerExtension extends Extension
{
    protected $container;
    
    /**
     * Loads the AssetPackager configuration.
     *
     * @param array $config  An array of configuration settings
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($config, ContainerBuilder $container)
    {
        $this->container = $container;
        $this->loadDefaults($config);
    }

    /**
     * Loads the default configuration.
     *
     * @param array $config An array of configuration settings
     */
    protected function loadDefaults(array $config)
    {
        if (!$this->container->hasDefinition('assetpackager')) {
            $loader = new XmlFileLoader($this->container, __DIR__ . '/../Resources/config');
            $loader->load('assetpackager.xml');
            $loader->load('controller.xml');
            $loader->load('templating.xml');
        }

        // Allow these application configuration options to override the defaults
        $options = array(
            'assets_path',
            'cache_path',
            'compress_assets',
            'package_assets',
        );

        foreach ($options as $key) {
            if (isset($config[$key])) {
                $this->container->setParameter('assetpackager.options.' . $key, $config[$key]);
            }

            $nKey = str_replace('_', '-', $key);
            if (isset($config[$nKey])) {
                $this->container->setParameter('assetpackager.options.' . $key, $config[$nKey]);
            }
        }

        if (false === isset($config['js']['compressor']) || null === $config['js']['compressor']) {
            $config['js']['compressor'] = array();
        }
        $this->container->setAlias('assetpackager.compressor.js', $this->resolveCompressorService($config['js']['compressor'], 'js', 'jsmin'));

        if (false === isset($config['css']['compressor']) || null === $config['css']['compressor']) {
            $config['css']['compressor'] = array();
        }
        $this->container->setAlias('assetpackager.compressor.css', $this->resolveCompressorService($config['css']['compressor'], 'css', 'cssmin'));
        
        if (isset($config['js']['packages'])) {
            $this->container->setParameter('assetpackager.packages.js', $config['js']['packages']);
        }

        if (isset($config['css']['packages'])) {
            $this->container->setParameter('assetpackager.packages.css', $config['css']['packages']);
        }
    }

    protected function resolveCompressorService($compressor, $format, $default)
    {
        $compressorService = null;

        if (is_scalar($compressor)) {
            $compressor = array(
                'id' => $compressor,
            );
        }

        if (false === isset($compressor['id']) && false === isset($compressor['options'])) {
            $compressor = array(
                'options' => $compressor,
            );
        }

        if (false === isset($compressor['id'])) {
            $compressor['id'] = $default;
        }

        if (false === isset($compressor['options'])) {
            $compressor['options'] = array();
        }

        if ($this->container->has($compressor['id'])) {
            $compressorService = $compressor['id'];
        } else if ($this->container->has("assetpackager.compressor.$format." . $compressor['id'])) {
            $compressorService = "assetpackager.compressor.$format." . $compressor['id'];
        } else {
            $compressorService = "assetpackager.compressor.$format.$default";
        }

        $this->container->setParameter("$compressorService.options", $compressor['options']);
        
        return $compressorService;
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://tecbot.de/schema/dic/assetpackager';
    }

    public function getAlias()
    {
        return 'assetpackager';
    }
}