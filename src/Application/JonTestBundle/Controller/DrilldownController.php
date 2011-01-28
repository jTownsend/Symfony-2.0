<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DrilldownController extends Controller
{
    public function indexAction($productName)
    {
	   	$product = array(
			'baby-bingo' => array(
				'magicZoom' 		=> 'images/assets/games/b/babybingo/drilldowns/babybingo_mzoom.png',
				'magicZoomPreview' 	=> 'images/assets/games/b/babybingo/drilldowns/babybingo_mzoom.png',
				'title'				=> 'Baby Bingo',
				'price'				=> '6.99',
				'personalize'		=> true,
				'description'		=> 'Super fun game',
				'timeToPlay'		=> '5 minutes',
				'players'			=> 6,
				'winners'			=> 2,
				'rulesOfPlay'		=> 'One player does this, and another player does that',
				'whatsIncluded'		=> '<ul><li>A Game</li><li>A lifetime of access</li></ul>',
				
			),
		);

		return $this->render('JonTestBundle:Jon:drilldown.twig.html', array('product' => $product[$productName]));
    }
}
