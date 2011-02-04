<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

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
		if ($this->get('request')->isXmlHttpRequest())
		{
			echo get_class(new Response('Test!'));
			return new Response();
		}
	}
	
	
}
