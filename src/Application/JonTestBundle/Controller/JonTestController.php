<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JonTestController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JonTestBundle:Jon:index.twig.html', array('name' => $name));

        // render a PHP template instead
        // return $this->render('HelloBundle:Hello:index.php.html', array('name' => $name));
    }
}
