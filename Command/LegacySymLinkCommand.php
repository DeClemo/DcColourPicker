<?php
/**
 * File containing the LegacySymLink command class
 *
 * @copyright Copyright (C) 2014 Springfoot Digital All rights reserved.
 * @license http://www.springfootdigital.com.au/Contact/Legal/MIT-License MIT License
 * @version
 */
 
namespace DanielClements\ColourPickerBundle\Command;
 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
 
class LegacySymLinkCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
    /**
     * Configures the command
     */
    protected function configure()
    {
        $this->setName( 'danielclements:colourpicker:legacysymlink' );
        //$this->setDefinition();
    }
 
    /**
     * Executes the command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        // Setting linking directories
        $currentDir = __DIR__;
        $sourceDir = $currentDir.'/../ezpublish_legacy/sfcolourpicker';
        $linkDir = $currentDir.'/../../../../ezpublish_legacy/extension/sfcolourpicker';

        $output->writeln( "Creating symlinks for eZ Legacy datatype..." );
        
        symlink($sourceDir, $linkDir);
        
        $output->writeln( "Completed." );
    }
}