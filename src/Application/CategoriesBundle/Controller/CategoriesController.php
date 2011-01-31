<?php

namespace Application\CategoriesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoriesController extends Controller
{
    public function indexAction()
    {
        /*$mailer = $this->get('mailer');
		
		$message = \Swift_Message::newInstance()
			->setSubject('Hello Email')
			->setFrom('jtownsend54@gmail.com')
			->setTo('jtownsend54@gmail.com')
			->setBody('Email working!');
			
		$mailer->send($message);*/
		
		$categories = array(
			'name' 				=> 'Printable Games',
			'categoriesSize' 	=> '4',
			'children' 			=> array(
				array(
					'url' 		=> '',
					'children' 	=> array(),
					'name'		=> 'Sweet',
					
				),
				array(
					'url' 		=> '',
					'children' 	=> array(),
					'name'		=> 'Dude',
					
				),
				array(
					'url' 		=> '',
					'children' 	=> array(),
					'name'		=> 'Whoa',
					
				),
				array(
					'url' 		=> '',
					'children' 	=> array(),
					'name'		=> 'What',
					
				),
			),		
		);
		
		return $this->render('CategoriesBundle:Categories:categories.twig.html', array('categories' => $categories));
    }
	
	public function headerAction($categoryName)
	{
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
		
		return $this->render('CategoriesBundle:Categories:header.twig.html', array('category' => $category[$categoryName]));	
	}
}
