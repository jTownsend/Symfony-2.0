<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\Javascript;

use Bundle\Tecbot\AssetPackagerBundle\Packager\Compressor\BaseCompressor;
use Symfony\Component\Process\Process;

class GoogleClosureCompressor extends BaseCompressor
{
    protected $executable;
    protected $compilationLevels = array('WHITESPACE_ONLY', 'SIMPLE_OPTIMIZATIONS', 'ADVANCED_OPTIMIZATIONS');

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
            throw new \InvalidArgumentException(sprintf('The GoogleClosureCompressor does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($defaultOptions, $options);
        $this->options['compilation_level'] = strtoupper($this->options['compilation_level']);

        // check compilation_level
        if (false === in_array($this->options['compilation_level'], $this->compilationLevels)) {
            throw new \InvalidArgumentException(sprintf('The GoogleClosureCompressor does not support the following compilation level: \'%s\'. Only %s.', $this->options['compilation_level'], implode(', ', $this->compilationLevels)));
        }

        // check vendor path
        if (false === is_file($this->options['path'])) {
            throw new \InvalidArgumentException(sprintf('The path of the closure-compiler not found (%s)', $this->options['path']));
        }

        $this->executable = sprintf('java -jar %s --compilation_level %s', $this->options['path'], $this->options['compilation_level']);
    }

    /**
     * {@inheritDoc}
     */
    public function compress($content)
    {
        $process = new Process($this->executable, null, array(), $content);
        $process->run();

        if (false === $process->isSuccessful()) {
            throw new \RuntimeException(sprintf('The GoogleClosureCompressor could not compress the package ([%s]: %s).', $process->getExitCode(), $process->getErrorOutput()));
        }

        return $process->getOutput();
    }

    /**
     * Returns the default config.
     * 
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
            'path' => $this->vendorPath . DIRECTORY_SEPARATOR . 'google-closure-compiler' . DIRECTORY_SEPARATOR . 'closure-compiler.jar',
        );
    }
}