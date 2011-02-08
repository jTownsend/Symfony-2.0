<?php

namespace Bundle\StoreBundle;

use Symfony\Component\EventDispatcher\Event,
	Symfony\Component\EventDispatcher\EventDispatcher;

class StoreListener
{
	private $dispatcher;
	
	public function __construct(EventDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
		echo 'Store :: ' . get_class($this->dispatcher);
		//echo 'Store listener constructed';
		/*if(is_callable(array($this, 'ajaxTestEvent')))
		{
			echo '<br />ajaxTestEvent is callable.';	
		}*/
		$this->dispatcher->connect('ajax.test_event',array($this, 'ajaxTestEvent'));
		/*if ($this->dispatcher->hasListeners('ajax.test_event'))
		{
			echo 'Has listeners';	
		}
		else
		{
			echo 'Has NO listeners';	
		}*/
	}
	
	public function ajaxTestEvent(Event $event, $arguments)
	{
		echo 'In the listener event';
		$arguments['ajaxEvent'] = 'Ajax event is working!';
		$event->setReturnValue($arguments);
		return $arguments;
	}
	
	
}
