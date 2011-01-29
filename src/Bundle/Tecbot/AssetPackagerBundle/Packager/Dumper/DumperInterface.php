<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Packager\Dumper;

interface DumperInterface
{
    /**
     * Returns a combined dump of files
     * 
     * @return string
     */
    function dump(array $files);
}