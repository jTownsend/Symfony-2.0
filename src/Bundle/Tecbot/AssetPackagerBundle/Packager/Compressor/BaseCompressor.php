<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor;

class BaseCompressor implements CompressorInterface
{
    protected $options = array();
    protected $vendorPath;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->vendorPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor');
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions(array $options = array(), $force = false)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}