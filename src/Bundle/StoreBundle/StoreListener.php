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
		echo 'Store listener constructed';
		$dispatcher->connect('ajaxTestEvent',array($this, 'ajaxTestEvent'));
	}
	
	public function ajaxTestEvent(Event $event, $arguments = array())
	{
		echo 'In the listener event';
		$arguments['ajaxEvent'] = 'Ajax event is working!';
		$event->setReturnValue($arguments);
		return $arguments;
	}
	
	
}
