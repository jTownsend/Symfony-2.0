<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JonTestController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JonTestBundle:Jon:content.twig.html', array('name' => $name));
    }
}
