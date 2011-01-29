<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor;

interface CompressorInterface
{    
    /**
     * Sets the compressor options.
     * 
     * @param array $options
     * @param boolean $force
     */
    function setOptions(array $options = array(), $force = false);
    
    /**
     * Compress a string.
     * 
     * @param string $content
     * 
     * @return string
     */
    function compress($content);
    
    /**
     * Return the options of the compressor.
     * 
     * @return mixed
     */
    function getOptions();
}