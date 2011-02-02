<?php
namespace Application\JonTestBundle;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class AssociationListener
{
	protected $_object = '';
	protected $_target = '';

    private $_updated = array();

    public function __construct($_object, $target)
    {
        $this->_object 	= (string) $_object;
		$this->_target	= (string) $target;
    }
	
	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		$mappings = $eventArgs->getClassMetadata()->associationMappings;
		
		if(isset($mappings[$this->_target]))
		{
			if (in_array($mappings[$this->_target]['targetEntity'], $this->_updated))
			{
				return;
			}
	
			if (strpos($mappings[$this->_target]['targetEntity'], $this->_object) !== false)
			{
				return;
			}
		
		
			$mappings[$this->_target]['targetEntity'] = $this->_object;
			$eventArgs->getClassMetadata()->associationMappings = $mappings;
			
			$this->_updated[] = $mappings[$this->_target]['targetEntity'];
		}
	}
}