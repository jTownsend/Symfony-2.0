<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\Javascript;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\BaseCompressor;

class PackerCompressor extends BaseCompressor
{
    /**
     * {@inheritDoc}
     */
    public function setOptions(array $options = array(), $force = false)
    {
        if (false === $force && !$diff = array_diff_assoc($options, $this->options)) {
            return;
        }

        $defaultOptions = $this->getDefaultOptions();

        // check option names
        if ($diff = array_diff(array_keys($options), array_keys($defaultOptions))) {
            throw new \InvalidArgumentException(sprintf('The PackerCompressor does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($defaultOptions, $options);

        // check vendor path
        if (false === is_file($this->options['path'])) {
            throw new \InvalidArgumentException(sprintf('The path of the JavaScriptPacker class not found (%s)', $this->options['path']));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        require_once $this->options['path'];

        $packer = new \JavaScriptPacker($content, $this->options['encoding'], $this->options['fast_decode'], $this->options['special_chars']);

        return $packer->pack();
    }

    /**
     * Returns the default config.
     * 
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'encoding' => 'Normal',
            'fast_decode' => true,
            'special_chars' => false,
            'path' => $this->vendorPath . DIRECTORY_SEPARATOR . 'packer' . DIRECTORY_SEPARATOR . 'packer.php',
        );
    }
}