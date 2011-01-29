<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;

class AssetPackagerClearCacheCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('assetpackager:clear-cache');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();

        $cachePath = $this->container->getParameter('assetpackager.options.cache_path');
        $finder = $finder->files()->in($cachePath);

        $filesystem = new Filesystem();
        $count = 0;
        foreach ($finder as $file) {
            $filesystem->remove($file);
            $count++;
        }

        $output->writeln("removed $count cache file(s)");
    }
}