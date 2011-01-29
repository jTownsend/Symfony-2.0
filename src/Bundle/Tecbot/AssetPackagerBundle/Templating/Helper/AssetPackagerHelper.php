<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Templating\Helper;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Manager;
use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\CompressorInterface;
use Bundle\Tecbot\AssetPackagerBundle\Packager\Dumper\DumperInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\Helper\Helper;

class AssetPackagerHelper extends Helper
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper 
     */
    protected $assetsHelper;
    /**
     * @var Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper
     */
    protected $routerHelper;
    /**
     * @var Bundle\Tecbot\AssetPackagerBundle\Packager\Manager 
     */
    protected $manager;
    /**
     * @var array
     */
    protected $options;
    /**
     * @var array 
     */
    protected $packages = array();

    /**
     * Constructor.
     * 
     * @param Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper $assetsHelper
     * @param Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper $routerHelper
     * @param Bundle\Tecbot\AssetPackagerBundle\Packager\Manager            $manager
     * @param array                                                         $options 
     */
    public function __construct(AssetsHelper $assetsHelper, RouterHelper $routerHelper, Manager $manager, array $options = array())
    {
        $this->assetsHelper = $assetsHelper;
        $this->routerHelper = $routerHelper;
        $this->manager = $manager;

        $this->options = array(
            'package_assets' => true,
            'compress_assets' => true,
        );

        // check option names
        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The AssetPackagerHelper does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Adds a asset package.
     *
     * @param string $packages A asset package
     * @param array  $attributes An array of attributes
     */
    public function add($packages, $format, $attributes = array())
    {
        
        if (false === $this->isValidFormat($format)) {
            throw new \InvalidArgumentException(sprintf('The AssetPackagerHelper does not support the following format: \'%s\'.', $format));
        }
        
        if(is_scalar($packages)) {
            $packages = array($packages);
        }
                
        foreach($packages as $package) {
            $this->packages[$format][$package] = $attributes;
        }
    }

    /**
     * Returns HTML representation of the links to packages.
     *
     * @return string The HTML representation of the packages
     */
    public function render($format = null)
    {
        if (null !== $format && false === $this->isValidFormat($format)) {
            throw new \InvalidArgumentException(sprintf('The AssetPackagerHelper does not support the following format: \'%s\'.', $format));
        }

        $html = '';
        
        if (isset($this->packages['css']) && (null === $format || 'css' === $format)) {
            $html .= $this->doRender($this->packages['css'], 'css');
        }

        if (isset($this->packages['js']) && (null === $format || 'js' === $format)) {
            $html .= $this->doRender($this->packages['js'], 'js');
        }

        return $html;
    }

    protected function doRender(array $packages, $format)
    {
        $html = '';
        foreach ($packages as $packageName => $attributes) {
            try {
                $package = $this->manager->get($packageName, $format);
                if (false === $package->options->get('package_assets', $this->options['package_assets'])) {
                    foreach ($package->paths as $path) {
                        $html .= $this->renderTag($this->assetsHelper->getUrl($path), $format, $attributes);
                    }
                    continue;
                }
                $html .= $this->renderTag($this->generatePackageURL($this->manager->compress($package), $format), $format, $attributes);
            } catch (\InvalidArgumentException $ex) {
                // No Package found
                $html .= $this->renderTag($this->assetsHelper->getUrl($packageName), $attributes);
            }
        }

        return $html;
    }

    /**
     * Outputs HTML representation of the links to packages.
     */
    public function output()
    {
        echo $this->render();
    }

    /**
     * Returns a string representation of this helper as HTML.
     *
     * @return string The HTML representation of the packages
     */
    public function __toString()
    {
        return $this->render();
    }
    
    protected function isValidFormat($format) {
        return in_array($format, array('js', 'css'));
    }

    /**
     * Generates a URL for a package.
     *
     * @param  string $file The URL of the package
     *
     * @return string The generated URL
     */
    protected function generatePackageURL($file, $format)
    {
        return $this->routerHelper->generate('_assetpackager_get', array('file' => $file, '_format' => $format));
    }

    /**
     * Render the html tag.
     *
     * @return String html tag
     */
    protected function renderTag($path, $format, array $attributes = array()) {
        $atts = '';
        foreach ($attributes as $key => $value) {
            $atts .= ' ' . sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, $this->assetsHelper->getCharset()));
        }
        
        if('css' === $format) {
            return sprintf('<link href="%s" rel="stylesheet" type="text/css"%s />', $path, $atts) . "\n";
        }

        return sprintf('<script type="text/javascript" src="%s"%s></script>', $path, $atts) . "\n";
    }
    
    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'assetpackager';
    }
}