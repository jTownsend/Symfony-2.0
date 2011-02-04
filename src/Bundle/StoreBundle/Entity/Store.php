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

namespace Bundle\StoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ignore
 */
use Doctrine\Common\Collections;

use Doctrine\ORM\Internal\Hydration;

/**
 * Entities\Store
 *
 * @orm:Table(name="store", indexes={
 * 	@orm:index(name="url", columns={"url"}),
 * 	@orm:index(name="checkout_key", columns={"checkout_key"})
 * })
 * @orm:Entity(repositoryClass="Bundle\StoreBundle\Entity\StoreRepository")
 */
class Store
{
	/**
	 * @orm:var integer $storeId
	 *
	 * @orm:Column(name="store_id", type="integer")
	 * @orm:Id
	 * @orm:GeneratedValue(strategy="AUTO")
	 */
	private $storeId;

	/**
	 * @orm:var string $name
	 *
	 * @orm:Column(name="name", type="string", length=64)
	 */
	private $name;

	/**
	 * @orm:var string $url
	 *
	 * @orm:Column(name="url", type="string", length=255)
	 */
	private $url;

	/**
	 * @orm:var string $title
	 *
	 * @orm:Column(name="title", type="string", length=128)
	 */
	private $title;

	/**
	 * @orm:var string $landingPage
	 *
	 * @orm:Column(name="landing_page", type="text")
	 */
	private $landingPage = '';

	/**
	 * @orm:var string $footerContent
	 *
	 * @orm:Column(name="footer_content", type="text")
	 */
	private $footerContent = '';

	/**
	 * @orm:var string $metaDescription
	 *
	 * @orm:Column(name="meta_description", type="string")
	 */
	private $metaDescription = '';

	/**
	 * @orm:var string $metaTagKeywords
	 *
	 * @orm:Column(name="meta_tag_keywords", type="string")
	 */
	private $metaTagKeywords = '';

	/**
	 * @orm:var string $template
	 *
	 * @orm:Column(name="template", type="string", length=64)
	 */
	private $template = '';

	/**
	 * @orm:var integer $siteCategory
	 *
	 * @orm:Column(name="site_category", type="integer", nullable=true)
	 */
	private $siteCategory;

	/**
	 * Default navigation category for home page
	 *
	 * @orm:Column(name="default_nav_category", type="integer", nullable=true)
	 */
	private $defaultNavCategory;

	/**
	 * @orm:var string $stylehseet
	 *
	 * @orm:Column(name="stylesheet", type="string", length=255, columnDefinition="VARCHAR(255) DEFAULT '' NOT NULL")
	 */
	private $stylesheet;

	/**
	 * Used in the url to determine what store we need to load the stylesheet for
	 *
	 * @orm:var integer $checkoutKey
	 *
	 * @orm:Column(name="checkout_key", type="string")
	 */
	private $checkoutKey = '';

	/**
	 * @orm:var integer $imageRelated
	 *
	 * @orm:Column(name="is_default", type="integer")
	 */
	private $isDefault = 0;

	/**
	 * @orm:var string $icon
	 *
	 * @orm:Column(name="icon", type="string", length=255)
	 */
	private $icon;

	/**
	 * @orm:var string $navImage
	 *
	 * @orm:Column(name="nav_image", type="array")
	 */
	private $navImageArray;

	/**
	 * @orm:var string $logo
	 *
	 * @orm:Column(name="logo", type="array")
	 */
	private $logoArray = array('w' => 0, 'h' => 0);

	/**
	 * @orm:var integer $imageThumb
	 *
	 * @orm:Column(name="image_thumb", type="array")
	 */
	private $imageThumb = array('width' => 250, 'height' => 250);

	/**
	 * @orm:var integer $imagePopup
	 *
	 * @orm:Column(name="image_popup", type="array")
	 */
	private $imagePopup = array('width' => 500, 'height' => 500);

	/**
	 * @orm:var integer $imageCategory
	 *
	 * @orm:Column(name="image_category", type="array")
	 */
	private $imageCategory = array('width' => 120, 'height' => 120);

	/**
	 * @orm:var integer $imageProduct
	 *
	 * @orm:Column(name="image_product", type="array")
	 */
	private $imageProduct = array('width' => 120, 'height' => 120);

	/**
	 * @orm:var integer $imageAdditional
	 *
	 * @orm:Column(name="image_additional", type="array")
	 */
	private $imageAdditional = array('width' => 150, 'height' => 150);

	/**
	 * @orm:var integer $imageRelated
	 *
	 * @orm:Column(name="image_related", type="array")
	 */
	private $imageRelated = array('width' => 120, 'height' => 120);

	/**
	 * @orm:var integer $imageCart
	 *
	 * @orm:Column(name="image_cart", type="array")
	 */
	private $imageCart = array('width' => 75, 'height' => 75);

	/**
	 * @orm:var string $googleSearch
	 *
	 * @orm:Column(name="google_search", type="string", nullable=true)
	 */
	private $googleSearch = '';

	/**
	 * @orm:var integer $shareASaleStoreId
	 *
	 * @orm:Column(name="shareasale_store_id", type="integer", nullable=true)
	 */
	private $shareASaleStoreId;

	/**
	 * @orm:var string $googleAnalyticsTrackingId
	 *
	 * @orm:Column(name="ga_tracking_id", type="string", nullable=true)
	 */
	private $googleAnalyticsTrackingId;

	/**
	 * Sets an array of data
	 * @param unknown_type $data
	 */
	public function setData($data)
	{
		foreach ($data as $name => $value)
		{
			$name = explode('_', $name);

			$column = '';
			foreach ($name as $key)
			{
				$column .= ucwords($key);
			}

			call_user_func(array($this, 'set' . $column), $value);
		}
	}

	function getStoreId() 
	{
		return $this->storeId;
	}

	/**
	 * @return the $name
	 */
	function getName() 
	{
		return $this->name;
	}

	/**
	 * @param $name the $name to set
	 */
	function setName($name) 
	{
		$this->name = $name;
	}

	/**
	 * @return the $url
	 */
	function getUrl() 
	{
		return $this->url;
	}

	/**
	 * @param $url the $url to set
	 */
	function setUrl($url) 
	{
		$this->url = $url;
	}

	/**
	 * @return the $title
	 */
	function getTitle() 
	{
		return $this->title;
	}

	/**
	 * @param $title the $title to set
	 */
	function setTitle($title) 
	{
		$this->title = $title;
	}

	/**
	 * @return the $landingPage
	 */
	function getLandingPage() 
	{
		return html_entity_decode($this->landingPage);
	}

	function setLandingPage($html) 
	{
		$this->landingPage = htmlentities($html);
	}

	/**
	 * @return the $footerContent
	 */
	function getFooterContent() 
	{
		return html_entity_decode($this->footerContent);
	}

	function setFooterContent($html) 
	{
		$this->footerContent = htmlentities($html);
	}

	/**
	 * @return the $metaDescription
	 */
	function getMetaDescription() 
	{
		return $this->metaDescription;
	}

	/**
	 * @param $metaDescription the $metaDescription to set
	 */
	function setMetaDescription($metaDescription) 
	{
		$this->metaDescription = $metaDescription;
	}

	/**
	 * @return the $metaTagKeywords
	 */
	function getMetaTagKeywords() 
	{
		return $this->metaTagKeywords;
	}

	/**
	 * @param $metaTagKeywords the $metaTagKeywords to set
	 */
	function setMetaTagKeywords($metaTagKeywords) 
	{
		$this->metaTagKeywords = $metaTagKeywords;
	}

	/**
	 * @return the $template
	 */
	function getTemplate() 
	{
		return $this->template;
	}

	/**
	 * @param $template the $template to set
	 */
	function setTemplate($template) 
	{
		$this->template = $template;
	}

	/**
	 * @return the $siteCategory
	 */
	function getSiteCategory() 
	{
		return $this->siteCategory;
	}

	function getSiteCategoryId() 
	{
		return $this->siteCategory;
	}

	/**
	 * @param $siteCategory the $siteCategory to set
	 */
	function setSiteCategory($siteCategory) 
	{
		$this->siteCategory = $siteCategory;
	}

	/**
	 * @return the $defaultNavCategory
	 */
	function getDefaultNavCategory() 
	{
		return $this->defaultNavCategory;
	}

	/**
	 * @param $defaultNavCategory the $defaultNavCategory to set
	 */
	function setDefaultNavCategory($defaultNavCategory) 
	{
		$this->defaultNavCategory = $defaultNavCategory;
	}

	/**
	 * @return the $stylesheet
	 */
	function getStylesheet() 
	{
		return $this->stylesheet;
	}

	/**
	 * @param $defaultNavCategory the $defaultNavCategory to set
	 */
	function setStylesheet($stylesheet) 
	{
		$this->stylesheet = $stylesheet;
	}

	/**
	 * @return the $checkoutKey
	 */
	function getCheckoutKey() 
	{
		return $this->checkoutKey;
	}

	/**
	 * @param $defaultNavCategory the $defaultNavCategory to set
	 */
	function setCheckoutKey($checkoutKey) 
	{
		$this->checkoutKey = $checkoutKey;
	}

	function getIsDefault() 
	{
		return $this->isDefault;
	}

	function setIsDefault($isDefault) 
	{
		$this->isDefault = $isDefault;
	}

	/**
	 * @return
	 */
	function getIcon() 
	{
		return $this->icon;
	}

	function setIcon($icon) 
	{
		$this->icon = $icon;
	}

	/**
	 * @param $navImageArray the $navImageArray to set
	 */
	function setNavImageArray($navImageArray) 
	{
		$this->navImageArray = $navImageArray;
	}

	function getNavImageArray() 
	{
		return $this->navImageArray;
	}

	function setLogoArray($logoArray) 
	{
		$this->logoArray = $logoArray;
	}

	/**
	 * @return
	 */
	function getLogoArray() 
	{
		return $this->logoArray;
	}

	/**
	 * @return the $logoArray image
	 */
	function getLogoImage() 
	{
		return $this->logoArray['image'];
	}

	/**
	 * @return the $logoArray width
	 */
	function getLogoWidth() 
	{
		return $this->logoArray['w'];
	}

	/**
	 * @return the $logoArray height
	 */
	function getLogoHeight() 
	{
		return $this->logoArray['h'];
	}

	/**
	 * @return the $navImageArray image
	 */
	function getNavImage() 
	{
		return $this->navImageArray['image'];
	}

	/**
	 * @return the $navImageArray width
	 */
	function getNavImageWidth() 
	{
		return $this->navImageArray['w'];
	}

	/**
	 * @return the $navImageArray height
	 */
	function getNavImageHeight() 
	{

		return $this->navImageArray['h'];
	}

	/**
	 * @return array The $imageThumb image
	 */
	function getImageThumb() 
	{
		return $this->imageThumb;
	}

	/**
	 * @return string The $imageThumb width
	 */
	function getImageThumbWidth() 
	{

		return $this->imageThumb['width'];
	}

	/**
	 * @return the $imageThumb height
	 */
	function getImageThumbHeight() 
	{

		return $this->imageThumb['height'];
	}

	/**
	 * @return array The $imagePopup image
	 */
	function getImagePopup() 
	{
		return $this->imagePopup;
	}

	/**
	 * @return string The $navImageArray width
	 */
	function getImagePopupWidth() 
	{

		return $this->imagePopup['width'];
	}

	/**
	 * @return the $navImageArray height
	 */
	function getImagePopupHeight() 
	{

		return $this->imagePopup['height'];
	}

	/**
	 * @return array The $imageCategory image
	 */
	function getImageCategory() 
	{
		return $this->imageCategory;
	}

	/**
	 * @return string The $imageCategory width
	 */
	function getImageCategoryWidth() 
	{

		return $this->imageCategory['width'];
	}

	/**
	 * @return the $imageCategory height
	 */
	function getImageCategoryHeight() 
	{

		return $this->imageCategory['height'];
	}

	/**
	 * @return array The $imageProduct image
	 */
	function getImageProduct() 
	{
		return $this->imageProduct;
	}

	/**
	 * @return string The $imageProduct width
	 */
	function getImageProductWidth() 
	{
		return $this->imageProduct['width'];
	}

	/**
	 * @return the $imageProduct height
	 */
	function getImageProductHeight() 
	{
		return $this->imageProduct['height'];
	}

	/**
	 * @return array The $imageAdditional image
	 */
	function getImageAdditional() 
	{
		return $this->imageProduct;
	}

	/**
	 * @return string The $imageAdditional width
	 */
	function getImageAdditionalWidth() 
	{
		return $this->imageAdditional['width'];
	}

	/**
	 * @return the $imageAdditional height
	 */
	function getImageAdditionalHeight() 
	{

		return $this->imageAdditional['height'];
	}

	/**
	 * @return array The $imageRelated image
	 */
	function getImageRelated() 
	{
		return $this->imageRelated;
	}

	/**
	 * @return string The $imageAdditional width
	 */
	function getImageRelatedWidth() 
	{

		return $this->imageRelated['width'];
	}

	/**
	 * @return the $imageAdditional height
	 */
	function getImageRelatedHeight() 
	{

		return $this->imageRelated['height'];
	}

	/**
	 * @return array The $imageCart image
	 */
	function getImageCart() 
	{
		return $this->imageCart;
	}

	/**
	 * @return string The $imageAdditional width
	 */
	function getImageCartWidth() 
	{

		return $this->imageCart['width'];
	}

	/**
	 * @return the $imageAdditional height
	 */
	function getImageCartHeight() 
	{

		return $this->imageCart['height'];
	}

	/**
	 * Get the google CSE id
	 */
	 function getGoogleSearch()
	 {
		return $this->googleSearch;
	 }

	 /**
	 * Set the google CSE id
	 */
	 function setGoogleSearch($id)
	 {
		$this->googleSearch = $id;
	 }

	/**
	 * Get the shareasale store id
	 */
	 function getShareASaleStoreId()
	 {
		return $this->shareASaleStoreId;
	 }

	 /**
	 * Set the shareasale store id
	 */
	 function setShareASaleStoreId($id)
	 {
		$this->shareASaleStoreId = $id;
	 }

	/**
	 * Get the Google Analytics Tracking id
	 */
	 function getGoogleAnalyticsTrackingId()
	 {
		return $this->googleAnalyticsTrackingId;
	 }

	 /**
	 * Set the Google Analytics Tracking id
	 */
	 function setGoogleAnalyticsTrackingId($id)
	 {
		$this->googleAnalyticsTrackingId = $id;
	 }
}