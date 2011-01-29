<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Dumper;

class Dumper implements DumperInterface
{
    /**
     * {@inheritDoc}
     */
    public function dump(array $files)
    {
        $content = '';

        foreach ($files as $file) {
            $content .= file_get_contents($file->getPath()) . "\n";
        }

        return $content;
    }
}