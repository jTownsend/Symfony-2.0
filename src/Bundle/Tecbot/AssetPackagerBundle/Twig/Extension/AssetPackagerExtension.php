<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Twig\Extension;

use Bundle\Tecbot\AssetPackagerBundle\Templating\Helper\AssetPackagerHelper;

class AssetPackagerExtension extends \Twig_Extension
{
    protected $helper;

    public function __construct(AssetPackagerHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'assetpackage' => new \Twig_Function_Method($this, 'add'),
            'assetpackages' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
        );
    }

    public function add($packages, $format, array $parameters = array())
    {
        $this->helper->add($packages, $format, $parameters);
    }

    public function render($format = null)
    {
        return $this->helper->render($format);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'assetpackager';
    }
}
