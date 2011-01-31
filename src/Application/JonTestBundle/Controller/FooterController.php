<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Doctrine\ORM\Query;

class FooterController extends Controller
{
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
		$query = $em->createQuery('SELECT s FROM Bundle\Ecommerce\StoreBundle\Entity\Store s');
		$stores = $query->getResult();
		
		foreach($stores as $store)
		{
			if($store->getCheckoutKey() == 'baby')
			{
				$curStore = $store;
				break;
			}
		}
		
		return $this->render('JonTestBundle:Jon:footer.twig.html', array('content' => $curStore->getFooterContent()));
    }
}
