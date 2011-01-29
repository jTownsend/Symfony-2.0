<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\CompressorInterface;
use Bundle\Tecbot\AssetPackagerBundle\Packager\Dumper\DumperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var Bundle\Tecbot\AssetPackagerBundle\Packager\Dumper\DumperInterface
     */
    protected $dumper;
    /**
     * @var array
     */
    protected $options;
    /**
     * @var array
     */
    protected $packages;
    /**
     * @var boolean
     */
    protected $initialized = false;

    /**
     * Constructor.
     * 
     * @param ContainerInterface $container
     * @param array $options
     * @param array $packages 
     */
    public function __construct(ContainerInterface $container, DumperInterface $dumper, array $options = array(), array $packages = array())
    {
        $this->container = $container;
        $this->dumper = $dumper;

        $this->options = array(
            'assets_path' => null,
            'cache_path' => null,
            'compress_assets' => true,
            'package_assets' => true,
            'debug' => false,
        );

        // check option names
        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The AssetPackager Manager does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($this->options, $options);
        $this->packages = array_merge(array(
            'js' => array(),
            'css' => array(),
                ), $packages);
    }

    /**
     * Initialize the packages
     */
    public function init()
    {
        $this->createCacheDirectory();
        $this->packages['js'] = is_array($this->packages['js']) ? $this->convertPackageInformation($this->packages['js'], 'js') : array();
        $this->packages['css'] = is_array($this->packages['css']) ? $this->convertPackageInformation($this->packages['css'], 'css') : array();

        $this->initialized = true;
    }

    /**
     * Returns all packages.
     * 
     * @return array
     */
    public function all()
    {
        if (false === $this->initialized) {
            $this->init();
        }

        return $this->packages;
    }

    /**
     * Returns a package.
     * 
     * @param string $package
     * @param string $format
     * 
     * @return Bundle\Tecbot\AssetPackagerBundle\Packager\Package
     */
    public function get($package, $format)
    {
        if (false === $this->initialized) {
            $this->init();
        }

        if (false === in_array($format, array('js', 'css'))) {
            throw new \InvalidArgumentException(sprintf('The AssetmanagerHelper does not support the following format: \'%s\'.', $format));
        }

        if (false === isset($this->packages[$format][$package])) {
            throw new \InvalidArgumentException(sprintf('Package for \'%s\' with format \'%s\' not found', $package, $format));
        }

        return $this->packages[$format][$package];
    }

    /**
     * Compress a package and returns the hash of the cache file.
     * 
     * @param Package $package A Package instance
     * 
     * @return string
     */
    public function compress(Package $package, $force = false)
    {
        if (false === $this->initialized) {
            $this->init();
        }

        $hash = (null !== $package->output) ? $package->output : md5($package->name . implode($package->paths));
        if (false !== $force || $this->needsReload($package, $hash)) {
            $package->content = $this->dumper->dump($package->files);
            if (false !== $package->options->get('compress_assets', $this->options['compress_assets'])) {
                $package->compressor->setOptions($package->compressorOptions);
                $package->content = $package->compressor->compress($package->content, $package->options->all());
            }

            $this->updateCache($package, $hash);
        }

        return $hash;
    }

    /**
     * Returns the content of a package.
     * 
     * @param string $hash
     * @param string $format
     * 
     * @return string
     */
    public function getContent($hash, $format)
    {
        $file = $this->getCacheFile($hash, $format);
        if (false === is_file($file)) {
            return false;
        }

        return file_get_contents($file);
    }

    /**
     * Converts packages.
     * 
     * @param array $packages
     * @param string $format
     * 
     * @return array 
     */
    protected function convertPackageInformation(array $packages, $format)
    {
        $convertedPackages = array();
        foreach ($packages as $name => $package) {
            if (false === isset($package['paths']) && false === isset($package['packages'])) {
                $package = array(
                    'paths' => is_array($package) ? array_values($package) : array(),
                );
            }

            $compressorClass = null;
            if (isset($package['compressor']) && null !== $package['compressor']) {
                if (is_scalar($package['compressor'])) {
                    $package['compressor'] = array(
                        'id' => $package['compressor'],
                    );
                }

                if (false === isset($package['compressor']['id']) && false === isset($package['compressor']['options'])) {
                    $package['compressor'] = array(
                        'options' => $package['compressor'],
                    );
                }

                if (false === isset($package['compressor']['id'])) {
                    $package['compressor']['id'] = "assetpackager.compressor.$format";
                }

                if (false === isset($package['compressor']['options'])) {
                    if ("assetpackager.compressor.$format" === $package['compressor']['id']) {
                        $package['compressor']['options'] = $this->container->get("assetpackager.compressor.$format")->getOptions();
                    } else {
                        $package['compressor']['options'] = array();
                    }
                }

                if ($this->container->has($package['compressor']['id'])) {
                    $compressorClass = $this->container->get($package['compressor']['id']);
                } else if ($this->container->has("assetpackager.compressor.$format." . $package['compressor']['id'])) {
                    $compressorClass = $this->container->get("assetpackager.compressor.$format." . $package['compressor']['id']);
                }
            } else {
                $package['compressor'] = array(
                    'options' => $this->container->get("assetpackager.compressor.$format")->getOptions(),
                );
            }

            if (null === $compressorClass || !$compressorClass instanceof CompressorInterface) {
                // Set default compressor
                $compressorClass = $this->container->get("assetpackager.compressor.$format");
            }

            $package['paths'] = isset($package['paths']) ? $package['paths'] : array();
            $package['options'] = array_merge($this->options, isset($package['options']) ? $package['options'] : array());
            $package['output'] = isset($package['output']) ? $package['output'] : null;
            $package['packages'] = isset($package['packages']) ? $package['packages'] : array();

            $convertedPackages[$name] = new Package($name, $format, $package['output'], $compressorClass, $package['compressor']['options'], $package['options'], $package['paths'], $package['packages']);
        }

        return $convertedPackages;
    }

    /**
     * Check the cache file.
     * 
     * @param Package $package
     * @param string $hash
     * 
     * @return boolean 
     */
    protected function needsReload(Package $package, $hash)
    {

        $file = $this->getCacheFile($hash, $package->format);
        if (false === file_exists($file)) {
            return true;
        }

        if ($this->options['debug'] === false) {
            return false;
        }

        $metadataFile = $this->getCacheFile($hash, 'meta');
        if (false === file_exists($metadataFile)) {
            return true;
        }

        $metadata = unserialize(file_get_contents($metadataFile));

        if ($diff = array_diff_assoc($package->options->all(), $metadata['options'])) {
            return true;
        }

        if ($metadata['compressor'] !== get_class($package->compressor)) {
            return true;
        }

        if ($diff = array_diff_assoc($package->compressorOptions, $metadata['compressor_options'])) {
            return true;
        }

        $time = filemtime($file);
        foreach ($metadata['files'] as $resource) {
            if (false === $resource->isUptodate($time)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update the cache file of the package.
     * 
     * @param Package $package
     * @param string $file
     */
    protected function updateCache(Package $package, $file)
    {
        $this->writeCacheFile($this->getCacheFile($file, $package->format), $package->content);

        if ($this->options['debug']) {
            $fileResources = array();
            foreach ($package->files as $packageFile) {
                $fileResources[] = new FileResource($packageFile->getPath());
            }

            if ($this->options['debug']) {
                $cacheData = array(
                    'options' => $package->options->all(),
                    'compressor' => get_class($package->compressor),
                    'compressor_options' => $package->compressorOptions,
                    'files' => $fileResources,
                );

                $this->writeCacheFile($this->getCacheFile($file, 'meta'), serialize($cacheData));
            }
        }
    }

    /**
     * Write a package cache file to the filesystem.
     * 
     * @throws \RuntimeException When cache file can't be wrote
     */
    protected function writeCacheFile($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            chmod($file, 0644);

            return;
        }

        throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }

    /**
     * Return the path of the cache file from a package.
     *
     * @param  string $file The cache file of the package
     * @param  string $format The format of the cache file
     *
     * @return string The path of the cach file
     */
    protected function getCacheFile($file, $format)
    {
        return $this->options['cache_path'] . DIRECTORY_SEPARATOR . $file . '.' . $format;
    }

    /**
     * Create the AssetPackager cache directory.
     */
    protected function createCacheDirectory()
    {
        if (false === is_dir($this->options['cache_path'])) {
            if (false === @mkdir($this->options['cache_path'], 0777, true)) {
                throw new \RuntimeException(sprintf('Unable to create the AssetPackager cache directory (%s)', dirname($this->options['cache_path'])));
            }
        } elseif (false === is_writable($this->options['cache_path'])) {
            throw new \RuntimeException(sprintf('Unable to write in the AssetPackager cache directory (%s)', $this->options['cache_path']));
        }
    }
}