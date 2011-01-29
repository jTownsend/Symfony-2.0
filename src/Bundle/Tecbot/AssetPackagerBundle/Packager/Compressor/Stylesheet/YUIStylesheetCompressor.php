<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\Stylesheet;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\BaseCompressor;
use Symfony\Component\Process\Process;

class YUIStylesheetCompressor extends BaseCompressor
{
    protected $executable;
    protected $commandOptions;

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
            throw new \InvalidArgumentException(sprintf('The YUIStylesheetCompressor does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($defaultOptions, $options);

        // check vendor path
        if (false === is_file($this->options['path'])) {
            throw new \InvalidArgumentException(sprintf('The path of the yui-compressor not found (%s)', $this->options['path']));
        }

        $this->executable = 'java -jar ' . $this->options['path'];
        $this->commandOptions = array('--type css', sprintf('--charset %s', $this->options['charset']), sprintf('--line-break %d', $this->options['line_break']));
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        $process = new Process($this->getCommandLine(), null, array(), $content);
        $process->run();

        if (false === $process->isSuccessful()) {
            throw new \RuntimeException(sprintf('The YUIStylesheetCompressor could not compress the package ([%s]: %s).', $process->getExitCode(), $process->getErrorOutput()));
        }

        return $process->getOutput();
    }

    /**
     * Returns the command line for the compress process
     * 
     * @return string
     */
    protected function getCommandLine()
    {
        return sprintf('%s %s', $this->executable, implode(' ', $this->commandOptions));
    }

    /**
     * Returns the default config.
     * 
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'charset' => 'utf-8',
            'line_break' => 0,
            'path' => $this->vendorPath . DIRECTORY_SEPARATOR . 'yui-compressor' . DIRECTORY_SEPARATOR . 'yuicompressor.jar',
        );
    }
}