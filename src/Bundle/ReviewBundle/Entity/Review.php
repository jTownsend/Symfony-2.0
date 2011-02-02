<?php
/**
 *
 * @package Printable Parties
 * @copyright (c) 2010 Funsational
 * @license Licensed for Funsational, Inc usage only.
 *
 * @author  (Michael Williams)
 *
 */

namespace Bundle\ReviewBundle\Entity;


/**
 * Entities\Review
 *
 * @orm:Table(name="review")
 * @orm:Entity(repositoryClass="EntitiesRepository\Review")
 */
class Review
{
	const PENDING 				= 0;

	const APPROVED 				= 1;

	const DISAPPROVED 			= 2;

	const FLAGGED	 			= 3;


	const PROFANITY				= 0;

	const WRONG_PRODUCT 		= 1;

	const SPAM 					= 2;

	const DUPLICATE				= 3;

	const COPYRIGHT				= 4;

	const NOT_PRODUCT_REVIEW	= 5;

	const OTHER					= 6;

	/**
	 * @orm:var integer $reviewId
	 *
	 * @orm:Column(name="id", type="integer")
	 * @orm:Id
	 * @orm:GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @orm:var string $author
	 *
	 * @orm:Column(name="author", type="string")
	 */
	private $author = '';

	/**
	 * @orm:var string $location
	 *
	 * @orm:Column(name="location", type="string")
	 */
	private $location = '';

	/**
	 * @orm:var string $title
	 *
	 * @orm:Column(name="title", type="string")
	 */
	private $title = '';

	/**
	 * @orm:var string $guests
	 *
	 * @orm:Column(name="guests", type="integer")
	 */
	private $guests = 0;

	/**
	 * @orm:var text $text
	 *
	 * @orm:Column(name="text", type="text")
	 */
	private $text = '';

	/**
	 * @orm:var integer $rating
	 *
	 * @orm:Column(name="rating", type="decimal", scale=1)
	 */
	private $rating = 0.0;

	/**
	 * @orm:var integer $status
	 *
	 * @orm:Column(name="status", type="integer")
	 */
	private $status;

	/**
	 * @orm:var datetime $dateAdded
	 *
	 * @orm:Column(name="date_added", type="datetime")
	 */
	private $dateAdded;

	/**
	 * @orm:var datetime $dateModified
	 *
	 * @orm:Column(name="date_modified", type="datetime")
	 */
	private $dateModified;

	/**
	 * @orm:ManyToOne(targetEntity="%bundlereview.review.product%", inversedBy="Reviews")
	 * @orm:JoinColumn(name="product_id", referencedColumnName="id", unique=false)
	 */
	private $Product;

	function __construct() {
		$this->dateAdded 	= new \DateTime('NOW');
		$this->dateModified = new \DateTime('NOW');
	}

	function getId() {
		return $this->id;
	}

	/**
	 * @return the $author
	 */
	function getAuthor() {
		return $this->author;
	}

	/**
	 * @return the $location
	 */
	function getLocation() {
		return $this->location;
	}

	/**
	 * @return the $title
	 */
	function getTitle() {
		return $this->title;
	}

	/**
	 * @return the $guests
	 */
	function getGuests() {
		return $this->guests;
	}

	/**
	 * @return the $text
	 */
	function getText() {
		return $this->text;
	}

	/**
	 * @return the $rating
	 */
	function getRating() {
		return $this->rating;
	}

	/**
	 * @return the $status
	 */
	function getStatus() {
		return $this->status;
	}

	/**
	 * @return the $dateAdded
	 */
	function getDateAdded() {
		return $this->dateAdded;
	}

	/**
	 * @return the $dateModified
	 */
	function getDateModified() {
		return $this->dateModified;
	}

	function getProduct() {
		return $this->Product;
	}

	/**
	 * @param $id the $id to set
	 */
	function setId($id) {
		$this->id = $id;
	}

	/**
	 * @param $author the $author to set
	 */
	function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @param $location the $location to set
	 */
	function setLocation($location) {
		$this->location = $location;
	}

	/**
	 * @param $title the $title to set
	 */
	function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @param $guests the $guests to set
	 */
	function setGuests($guests) {
		$this->guests = $guests;
	}

	/**
	 * @param $text the $text to set
	 */
	function setText($text) {
		$this->text = $text;
	}

	/**
	 * @param $rating the $rating to set
	 */
	function setRating($rating) {
		$this->rating = $rating;
	}

	/**
	 * @param $dateAdded the $dateAdded to set
	 */
	function setDateAdded(\DateTime $dateAdded) {
		$this->dateAdded = $dateAdded;
	}

	/**
	 * @param $dateModified the $dateModified to set
	 */
	function setDateModified(\DateTime $dateModified) {
		$this->dateModified = $dateModified;
	}

	function setProduct($product) {
		$this->Product = $product;
	}
}