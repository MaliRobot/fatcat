<?php


namespace App\Controller;


use App\Services\CustomerService;
use App\Services\SoapService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class SoapController extends AbstractController
{
    /**
     * @Route("/soap")
     */
    public function soapAction(SoapService $soapService)
    {
        $soapServer = new \SoapServer('http://fatcat/fatcat.wsdl'); //, $customerService->getCustomersOrders());
        $soapServer->setObject($soapService);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }
}