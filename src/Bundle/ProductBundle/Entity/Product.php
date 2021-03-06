<?php
/**
 *
 * @package Printable Parties
 * @copyright (c) 2011 Funsational
 * @license Licensed for Funsational, Inc usage only.
 *
 * @author  (Jon Townsend)
 *
 */
 
namespace Bundle\ProductBundle\Entity;

/**
 * @orm:Entity
 * @orm:Table(name="product")
 */
class Product
{
	/**
     * @orm:Column(name="id", type="integer")
     * @orm:Id
     * @orm:GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	/**
     * @orm:var string $title
     *
     * @orm:Column(name="title", type="string", length=255)
     */
    private $title = '';

    /**
     * @orm:var string $URLTitle
     *
     * @orm:Column(name="url_title", type="string", length=255)
     */
    private $URLTitle = '';
	
	/**
     * @orm:var string $price
     *
     * @orm:Column(name="price", type="decimal", scale=2)
     */
    private $price = 0.00;
	
	/**
	 * @orm:OneToMany(targetEntity="%productbundle.product.review%", mappedBy="Product")
	 * @orm:OrderBy({"dateAdded" = "DESC"})
	 */
	private $Reviews;
	
	/**
	 * @orm:OneToMany(targetEntity="%productbundle.product.customer%", mappedBy="Product")
	 * @orm:OrderBy({"dateAdded" = "DESC"})
	 */
	private $Customer;
	
	/**
	 * Set the title of the product
	 * 
	 * @param $title  The product title
	 */
	public function setTitle($title)
	{
		$this->title = $title; 
	}
	
	/**
	 * Get the reviews for this product
	 */
	public function getReviews()
	{
		return $this->Reviews; 
	}
}