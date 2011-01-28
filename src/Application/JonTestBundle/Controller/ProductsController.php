<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductsController extends Controller
{
    public function indexAction($categoryName)
    {
        $products = array(
			'sweet' => array(
				array(
					'title' 		=> 'Baby Bingo',
					'thumb' 		=> 'images/assets/games/b/babybingo/drilldowns/babybingo_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> true,
					'personalize' 	=> false,
					'urlTitle'		=> 'baby-bingo',
				),
				array(
					'title' 		=> 'Bridal Bingo',
					'thumb' 		=> 'images/assets/games/b/bridalbingo/drilldowns/bridalbingo_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'bridal-bingo',
				),
			),
			'dude' => array(
				array(
					'title' 		=> 'Animal Babies Match',
					'thumb' 		=> 'images/assets/games/a/animalbabiesmatch/drilldowns/animalbabiesmatch_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> false,
					'urlTitle'		=> 'animal-babies-match',
				),
				array(
					'title' 		=> 'Noah\'s Ark',
					'thumb' 		=> 'images/assets/games/n/noahsark/drilldowns/noahsark_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'noahs-ark',
				),
				array(
					'title' 		=> 'Family Ties',
					'thumb' 		=> 'images/assets/games/f/familyties/drilldowns/familyties_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'family-ties',
				),
			),
			'whoa' => array(
				array(
					'title' 		=> 'Halloween Word Scramble',
					'thumb' 		=> 'images/assets/games/h/halloweenwordscramble/drilldowns/halloweenwordscramble_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> false,
					'urlTitle'		=> 'halloween-word-scramble',
				),
				array(
					'title' 		=> 'Harvest Word Puzzle',
					'thumb' 		=> 'images/assets/games/h/harvestwordpuzzle/drilldowns/harvestwordpuzzle_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'harvest-word-puzzle',
				),
				array(
					'title' 		=> 'Finish The Spooky Phrase',
					'thumb' 		=> 'images/assets/games/f/finishthespookyphrase/drilldowns/finishthespookyphrase_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'finish-the-spooky-phrase',
				),
			),
			'what' => array(
				array(
					'title' 		=> 'Bible Pairs',
					'thumb' 		=> 'images/assets/games/b/biblepairs/drilldowns/biblepairs_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> false,
					'urlTitle'		=> 'bible-pairs',
				),
				array(
					'title' 		=> 'The Topic Of The Day Is Red',
					'thumb' 		=> 'images/assets/games/t/thetopicofthedayisred/drilldowns/thetopicofthedayisred_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'the-topic-of-the-day-is-red',
				),
				array(
					'title' 		=> 'Movie Love Quotes',
					'thumb' 		=> 'images/assets/games/m/movielovequotes/drilldowns/movielovequotes_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'movie-love-quotes',
				),
				array(
					'title' 		=> 'Valentine Fun Candy Trivia',
					'thumb' 		=> 'images/assets/games/v/valentinefuncandytrivia/drilldowns/valentinefuncandytrivia_mzoom.png',
					'price' 		=> '6.99',
					'new' 			=> false,
					'personalize' 	=> true,
					'urlTitle'		=> 'valentine-fun-candy-trivia',
				),
			),
		);
		
		$category = array(
			'sweet' => array(
				'title' 	=> $categoryName,
				'image' 	=> 'images/assets/categories/baby_shower_games_atoz/catg_bingo.png',
				'count'		=> 2,
			),
			'dude' => array(
				'title' 	=> $categoryName,
				'image' 	=> 'images/assets/categories/baby_shower_games_atoz/catg_animal.png',
				'count'		=> 3,
			),
			'whoa' => array(
				'title' 	=> $categoryName,
				'image' 	=> 'images/assets/categories/baby_shower_games_atoz/catg_easy.png',
				'count'		=> 3,
			),
			'what' => array(
				'title' 	=> $categoryName,
				'image' 	=> 'images/assets/categories/baby_shower_games_atoz/catg_word.png',
				'count'		=> 4,
			),
		);

		return $this->render('JonTestBundle:Jon:products.twig.html', array('products' => $products[$categoryName], 'category' => $category[$categoryName]));
    }
}
