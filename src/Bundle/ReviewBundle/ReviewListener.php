<?php
namespace Bundle\ReviewBundle;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class ReviewListener
{
	protected $_reviewObject = '';
	protected $_target = '';

    private $_updated = array();

    public function __construct($reviewObject, $target)
    {
        $this->_reviewObject 	= (string) $reviewObject;
		$this->_target			= (string) $target;
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
	
			if (strpos($mappings[$this->_target]['targetEntity'], $this->_reviewObject) !== false)
			{
				return;
			}
		
		
			$mappings[$this->_target]['targetEntity'] = $this->_reviewObject;
			$eventArgs->getClassMetadata()->associationMappings = $mappings;
			
			$this->_updated[] = $mappings[$this->_target]['targetEntity'];
		}
	}
}