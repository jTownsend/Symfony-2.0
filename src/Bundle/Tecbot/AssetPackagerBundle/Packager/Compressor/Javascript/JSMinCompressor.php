<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\Javascript;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\BaseCompressor;

class JSMinCompressor extends BaseCompressor
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
            throw new \InvalidArgumentException(sprintf('The JSMinCompressor does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($defaultOptions, $options);
       

        // check vendor path
        if (false === is_file($this->options['path'])) {
            throw new \InvalidArgumentException(sprintf('The path of the JSMin class not found (%s)', $this->options['path']));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        require_once $this->options['path'];

        return \JSMin::minify($content);
    }

    /**
     * Returns the default config.
     * 
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'path' => $this->vendorPath . DIRECTORY_SEPARATOR . 'jsmin-php' . DIRECTORY_SEPARATOR . 'jsmin.php',
        );
    }
}