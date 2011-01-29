<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\CompressorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ParameterBag;

class Package
{
    public $name;
    public $format;
    public $output;
    public $compressor;
    public $compressorOptions;
    public $options;
    public $paths;
    public $files;
    public $embeddedPackages;
    public $content;

    public function __construct($name, $format, $output, CompressorInterface $compressor, array $compressorOptions, array $options = array(), array $paths = array(), array $embeddedPackages = array())
    {
        $this->name = $name;
        $this->format = $format;
        $this->output = $output;
        $this->compressor = $compressor;
        $this->compressorOptions = $compressorOptions;
        $this->options = new ParameterBag($options);
        $this->paths = $paths;
        $this->files = $this->convertPaths($this->paths);
        $this->embeddedPackages = $embeddedPackages;
    }

    public function hasEmbeddedPackages()
    {
        return $this->embeddedPackages != null && count($this->embeddedPackages) > 0 ? true : false;
    }

    protected function convertPaths(array $paths)
    {
        $files = array();
        $paths = array_unique($paths);

        foreach ($paths as $path) {
            if (!is_file($path)) {
                $path = $this->options->get('assets_path') . DIRECTORY_SEPARATOR . $path;
            }

            try {
                $files[] = new File($path);
            } catch (FileNotFoundException $ex) {
                throw new \InvalidArgumentException(sprintf('File %s of package %s not found: %s', $path, $this->name, $ex->getMessage()));
            }
        }

        return $files;
    }
}