<?php
namespace DoctrineExtensions;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class TablePrefixListener
{
    protected $_prefix = '';

    private $_updated = array();

    public function __construct($prefix)
    {
        $this->_prefix = (string) $prefix;
    }
	
	/**
     * Registers a loadClassMetadata listeners.
     *
     * @param EventDispatcher $dispatcher An EventDispatcher instance
     * @param integer         $priority   The priority
     */
    /*public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('loadClassMetadata', array($this, 'loadClassMetadata'), $priority);
    }*/

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

    	if (in_array($classMetadata->getTableName(), $this->_updated))
    	{
    		return;
    	}

    	if (strpos($classMetadata->getTableName(), $this->_prefix) !== false)
    	{
    		return;
    	}

        $classMetadata->setTableName($this->_prefix . $classMetadata->getTableName());

        $this->_updated[] = $classMetadata->getTableName();
    }
}