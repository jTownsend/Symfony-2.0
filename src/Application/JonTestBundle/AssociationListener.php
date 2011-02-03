<?php
namespace Application\JonTestBundle;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\Container;

class AssociationListener
{
	protected $_container = '';
	//protected $_target = '';

    private $_updated = array();

    public function __construct(Container $container)
    {
        $this->_container 	=  $container;
		//$this->_target	= (string) $target;
    }
	
	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		$mappings = $eventArgs->getClassMetadata()->associationMappings;
		$matches = array();
		foreach($mappings as $key => $mapping)
		{
			preg_match('#%(.*)%#', $mappings[$key]['targetEntity'], $matches);
			$mappings[$key]['targetEntity'] = $this->_container->getParameter($matches[1]);
		}
		
		$eventArgs->getClassMetadata()->associationMappings = $mappings;
		/*var_dump($mappings);
		die;*/
	}
}