<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Symfony\Component\HttpFoundation\Response,
	Symfony\Component\EventDispatcher\Event,
	Symfony\Component\EventDispatcher\EventDispatcher;

class ContentController extends Controller
{
    private $ajaxResponse = array();
	
	
	public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
		$query = $em->createQuery('SELECT s FROM Bundle\StoreBundle\Entity\Store s');
		$stores = $query->getResult();
		
		foreach($stores as $store)
		{
			if($store->getCheckoutKey() == 'baby')
			{
				$curStore = $store;
				break;
			}
		}
		
		$content = array(
			'banner' => 'images/assets/banners/billboards/baby_shower_games_atoz/billboard_final_baby.png',
			'bingoSale' => 'images/assets/banners/sales/baby_shower_games_atoz/bingosale_babytitle.png'
		);

		return $this->render('JonTestBundle:Jon:content.twig.html', array('content' => $curStore->getLandingPage()));
    }
	
	public function ajaxAction()
	{
		$dispatcher = $this->get('event_dispatcher');
		echo 'Content :: ' . get_class($dispatcher);
		$event = new Event($this, 'ajax.test_event');
		$dispatcher->filter($event, array());
		
		/*if ($dispatcher->hasListeners('ajax.test_event'))
		{
			echo 'Has listeners';	
		}
		else
		{
			echo 'Has NO listeners';	
		}*/
		
		$arguments = $event->getReturnValue();
		//echo '<br />' . sizeof($arguments) . '<br />';
		if ($this->get('request')->isXmlHttpRequest())
		{
			$response = array(
				'message' 	=> 'Working!',
				'status'	=> 1
			);
			$response = array_merge($response, $arguments);
			return new Response(json_encode($response));
		}
	}	
}
