<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JonTestController extends Controller
{
    public function indexAction()
    {
        return $this->render('JonTestBundle:Jon:content.twig.html');
    }
}
