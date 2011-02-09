<?php

namespace Bundle\StoreBundle;

use Symfony\Component\EventDispatcher\Event,
	Symfony\Component\EventDispatcher\EventDispatcher;

class StoreListener
{	
	public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('ajax.test_event', array($this, 'ajaxTestEvent'), $priority);
    }
	
	public function ajaxTestEvent(Event $event, $arguments)
	{
		$arguments['ajaxEvent'] = 'Ajax event is working!';
		$event->setReturnValue($arguments);
		return $arguments;
	}
	
	
}
