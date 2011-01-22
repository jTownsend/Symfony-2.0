<?php

namespace Symfony\Bundle\FrameworkBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameConverter;
use Symfony\Component\Routing\Loader\DelegatingLoader as BaseDelegatingLoader;
use Symfony\Component\Routing\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * DelegatingLoader delegates route loading to other loaders using a loader resolver.
 *
 * This implementation resolves the _controller attribute from the short notation
 * to the fully-qualified form (from a:b:c to class:method).
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class DelegatingLoader extends BaseDelegatingLoader
{
    protected $converter;
    protected $logger;

    /**
     * Constructor.
     *
     * @param ControllerNameConverter $converter A ControllerNameConverter instance
     * @param LoggerInterface         $logger    A LoggerInterface instance
     * @param LoaderResolverInterface $resolver  A LoaderResolverInterface instance
     */
    public function __construct(ControllerNameConverter $converter, LoggerInterface $logger = null, LoaderResolverInterface $resolver)
    {
        $this->converter = $converter;
        $this->logger = $logger;

        parent::__construct($resolver);
    }

    /**
     * Loads a resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return RouteCollection A RouteCollection instance
     */
    public function load($resource, $type = null)
    {
        $collection = parent::load($resource, $type);

        foreach ($collection->all() as $name => $route) {
            if ($controller = $route->getDefault('_controller')) {
                try {
                    $controller = $this->converter->fromShortNotation($controller);
                } catch (\Exception $e) {
                    // unable to optimize unknown notation
                }

                $route->setDefault('_controller', $controller);
            }
        }

        return $collection;
    }
}
