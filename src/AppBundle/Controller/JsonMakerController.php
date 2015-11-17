<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\DbObject\HostCollection;


class JsonMakerController extends Controller{
    public function indexAction(Request $request)
    {
        $host = new HostCollection();
        $response = new Response();
        $response->setContent(json_encode($host->fullDump()));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
