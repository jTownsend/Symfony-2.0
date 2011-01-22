<?php

namespace Application\JonTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FooterController extends Controller
{
    public function indexAction()
    {
        return $this->render('JonTestBundle:Jon:footer.twig.html');
    }
}
