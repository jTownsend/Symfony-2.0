<?php

namespace Bundle\Tecbot\AssetPackagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AssetPackagerController extends Controller
{
    public function getAction($file)
    {
        $format = $this->get('request')->getRequestFormat();
        
        if (false !== $content = $this->get('assetpackager.manager')->getContent($file, $format)) {
            $contentType = ($format == 'css') ? 'text/css' : 'application/x-javascript';
            return $this->createResponse($content, 200, array('Content-Type' => $contentType));
        }
        
        return $this->createResponse('', 404);
    }
}