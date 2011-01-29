<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;

class AssetPackagerCompressPackagesCommand extends Command
{
    /**
     * @var Bundle\Tecbot\AssetPackagerBundle\Packager\Manager
     */
    protected $manager;
    /**
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;
    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('assetpackager:compress-packages');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->manager = $this->container->get('assetpackager.manager');
        $this->output = $output;
        $packages =  $this->manager->all();
        
        $this->output->writeln(sprintf('compress %d javascript packages...', count($packages['js'])));
        $this->doCompress($packages['js']);
        
        $this->output->writeln(sprintf('compress %d stylesheet packages...', count($packages['css'])));
        $this->doCompress($packages['css']);

        $this->output->writeln('all packages compressed');
    }
    
    protected function doCompress(array $packages) {
        foreach($packages as $package) {
            $this->output->writeln(sprintf('compress %s package (Files: %d)...', $package->name,  count($package->files)));
            $this->manager->compress($package, true);
        }
    }
}