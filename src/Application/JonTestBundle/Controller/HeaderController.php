<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HeaderController extends Controller
{
    public function indexAction()
    {
         $stores = array(
			'Baby',
			'Bridal',
			'Birthday'
		);
		return $this->render('JonTestBundle:Jon:header.twig.html', array('stores' => $stores));
    }
}
