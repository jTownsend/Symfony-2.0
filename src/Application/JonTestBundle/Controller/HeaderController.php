<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HeaderController extends Controller
{
    public function indexAction()
    {
         $stores = array(
			array(
				  'title'		=> 'Bridal',
				  'href'		=> 'bridal',
				  'nav_image'	=> 'images/assets/logos/navigation/bridal_shower_games_atoz/header.png',
				  'nav_image_w'	=> '37px',
				  'nav_image_h'	=> '59px'
				  ),
			array(
				  'title'		=> 'Baby',
				  'href'		=> 'baby',
				  'nav_image'	=> 'images/assets/logos/navigation/baby_shower_games_atoz/header.png',
				  'nav_image_w'	=> '39px',
				  'nav_image_h'	=> '60px'
				  ),
			array(
				  'title'		=> 'Birthday',
				  'href'		=> 'birthday',
				  'nav_image'	=> 'images/assets/logos/navigation/birthday_games_atoz/header.png',
				  'nav_image_w'	=> '40px',
				  'nav_image_h'	=> '55px'
				  )
		);
		
		$headerCategories = array(
			array(
				'catTitle' => 'New Games'
			),
			array(
				'catTitle' => 'Specials'
			),
			array(
				'catTitle' => 'Personalize It!'
			),
		);
		
		$headerCategoryLength = sizeof($headerCategories);
		$counter = 0;
		$navBar = '';
		foreach ($headerCategories as $category)
		{
			$navBar .= '<a href="">' . $category['catTitle'] . '</a>';
			if($headerCategoryLength !== $counter)
			{
				$navBar .= '<div>&bull;&bull;</div>';
			}
			$counter++;
		}

		if ($headerCategoryLength < 4)
		{
			$navBar .= '<a href="">About Us</a>';
		} 
		
		$header = array(
			'logo'					=> 'images/assets/logos/header/baby_shower_games_atoz/logo.png',
			'store'					=> 'Baby Shower Games AtoZ',
			'logo_image_w'			=> '427px',
			'logo_image_h'			=> '84px',
			'navCategories'			=> $headerCategories,
			'navCategoriesLength'	=> sizeof($headerCategories)
		);
		
		
		return $this->render('JonTestBundle:Jon:header.twig.html', array('stores' => $stores, 'store_title' => 'Baby', 'header' => $header));
    }
}
