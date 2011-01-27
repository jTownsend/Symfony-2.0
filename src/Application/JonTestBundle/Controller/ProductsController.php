<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductsController extends Controller
{
    public function indexAction($categoryName)
    {
        $products = array(
			'sweet' => array(
				'image' => 'images/assets/categories/baby_shower_games_atoz/catg_bingo.png',
				array(
					'title' => 'Baby Bingo',
					'thumb' => 'images/assets/games/b/babybingo/drilldowns/babybingo_mzoom.png',
					'price' => '6.99',
				),
				array(
					'title' => 'Bridal Bingo',
					'thumb' => 'images/assets/games/b/bridalbingo/drilldowns/bridalbingo_mzoom.png',
					'price' => '6.99',
				),
			),
			'dude' => '',
			'whoa' => '',
			'what' => '',
		);

		return $this->render('JonTestBundle:Jon:products.twig.html', array('products' => $products[$categoryName], 'category' => $categoryName));
    }
}
