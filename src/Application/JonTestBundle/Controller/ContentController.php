<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContentController extends Controller
{
    public function indexAction()
    {
        $content = array(
			'banner' => 'images/assets/banners/billboards/baby_shower_games_atoz/billboard_final_baby.png',
			'bingoSale' => 'images/assets/banners/sales/baby_shower_games_atoz/bingosale_babytitle.png'
		);

		return $this->render('JonTestBundle:Jon:content.twig.html', array('content' => $content));
    }
}
