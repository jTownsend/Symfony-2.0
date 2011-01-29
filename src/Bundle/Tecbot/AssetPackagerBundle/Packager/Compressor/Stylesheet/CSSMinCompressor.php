<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\Stylesheet;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\BaseCompressor;

class CSSMinCompressor extends BaseCompressor
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
            throw new \InvalidArgumentException(sprintf('The CSSMinCompressor does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($defaultOptions, $options);

        // check vendor path
        if (false === is_file($this->options['path'])) {
            throw new \InvalidArgumentException(sprintf('The path of the CSSMin class not found (%s)', $this->options['path']));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        require_once $this->options['path'];

        return \CSSMin::minify($content, $this->options);
    }

    /**
     * Returns the default config.
     * 
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'remove-empty-blocks' => true,
            'remove-empty-rulesets' => true,
            'remove-last-semicolons' => true,
            'convert-css3-properties' => false,
            'convert-color-values' => false,
            'compress-color-values' => false,
            'compress-unit-values' => false,
            'emulate-css3-variables' => true,
            'path' => $this->vendorPath . DIRECTORY_SEPARATOR . 'cssmin' . DIRECTORY_SEPARATOR . 'cssmin.php',
        );
    }
}