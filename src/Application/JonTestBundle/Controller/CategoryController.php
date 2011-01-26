<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function indexAction()
    {
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
		
		return $this->render('JonTestBundle:Jon:category.twig.html', array('categories' => $categories));
    }
}
