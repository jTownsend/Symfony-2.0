<?php
namespace Bundle\ProductBundle;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class ProductListener
{
	protected $_productObject = '';
	protected $_target = '';

    private $_updated = array();

    public function __construct($productObject, $target)
    {
        $this->_productObject 	= (string) $productObject;
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
	
			if (strpos($mappings[$this->_target]['targetEntity'], $this->_productObject) !== false)
			{
				return;
			}
		
		
			$mappings[$this->_target]['targetEntity'] = $this->_productObject;
			$eventArgs->getClassMetadata()->associationMappings = $mappings;
			
			$this->_updated[] = $mappings[$this->_target]['targetEntity'];
		}
	}
}