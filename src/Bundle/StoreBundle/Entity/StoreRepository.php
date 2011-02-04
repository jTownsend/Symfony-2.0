<?php

namespace Bundle\StoreBundle\Entity;

use Doctrine\ORM\EntityRepository,
	Doctrine\ORM\Query;

/**
 *
 * @package Printable Parties
 * @copyright (c) 2010 Funsational
 * @license Licensed for Funsational, Inc usage only.
 *
 * @author  Michael Williams <michael.williams@funsational.com>
 */
class StoreRepository extends EntityRepository
{
	public function getStores()
	{
		$qb = $this->_em->createQueryBuilder();
		$qb->select('s')	
			->from('Bundle\StoreBundle\Entity\Store', 's');
			
		return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
	}
}