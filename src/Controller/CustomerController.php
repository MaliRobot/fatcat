<?php


namespace App\Controller;

use App\Entity\Customer;
use App\Services\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\CustomerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use ProxyManager\Factory\RemoteObject\Adapter\Soap;


/**
 * @Route("/api")
 */
class CustomerController extends AbstractController
{
    /**
    * @SWG\Get(
    *     @SWG\Response(
    *         response=200,
    *         description="Returns all customers"
    *     ),
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Customer::class)
    *     ),
    *     @SWG\Response(
    *         response=404,
    *         description="Returns an error when no customers were found"
    *     )
    * )
    * @Route("/customers", name="customers", methods={"GET", "HEAD"})
    */
    public function getCustomerAction(Request $request, CustomerService $customerService){
        $customers = $customerService->getCustomers($request);
        return new JsonResponse($customers);
    }

    /**
    * @SWG\Get(
    *     @SWG\Parameter(
    *         name="id",
    *         description="ID of customer that needs to be fetched",
    *         required=true,
    *         in="path",
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response=200,
    *         description="Return a certain customer"
    *     ),
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Customer::class)
    *     ),
    *     @SWG\Response(
    *         response=404,
    *         description="Returns an error when no customer is found"
    *     )
    * )
    * @Route("/customers/{customer}", name="single_customer", methods={"GET", "HEAD"})
    * @ParamConverter("customer", class="App\Entity\Customer")
    */
    public function getSingleCustomerAction($customer, CustomerService $customerService){
        $customer = $customerService->getSingleCustomer($customer);
        return new JsonResponse($customer);
    }

    /**
    * @SWG\Post(
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Customer::class)
    *     ),
    *     @SWG\Parameter(
    *         name="request",
    *         description="New customer data",
    *         required=true,
    *         in="body",
    *         type="object",
    *         @SWG\Property(property="first_name", type="string"),
    *         @SWG\Property(property="last_name", type="string"),
    *         @SWG\Property(property="email", type="string"),
    *         @SWG\Property(property="street", type="string"),
    *         @SWG\Property(property="country", type="string")
    *     ),
    *     @SWG\Response(
    *         response="200",
    *             description="Returned when successful",
    *             @Model(type=App\Entity\Customer::class)
    *         ),
    *     @SWG\Response(response="400",description="Returned when the data is missing or data is not correct"),
    *     @SWG\Response(response="500",description="Returned when server side error occurred")
    * )
    * @Route("/customers", name="create_customer", methods={"POST"})
    */
    public function createCustomerAction(Request $request, CustomerService $customerService){
        $result = $customerService->create($request);
        return new JsonResponse($result);
    }

    /**
    * @SWG\Patch(
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Customer::class)
    *     ),
    *     @SWG\Parameter(
    *         name="request",
    *         description="Change customer data",
    *         required=true,
    *         in="body",
    *         type="object",
    *         @SWG\Property(property="first_name", type="string"),
    *         @SWG\Property(property="last_name", type="string"),
    *         @SWG\Property(property="email", type="string"),
    *         @SWG\Property(property="street", type="string"),
    *         @SWG\Property(property="country", type="string"),
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="Returned when successful",
    *         @Model(type=App\Entity\Customer::class)
    *     ),
    *     @SWG\Response(response="400",description="Returned when the data is missing or data is not correct"),
    *     @SWG\Response(response="500",description="Returned when server side error occurred")
    * )
    * @Route("/customers/{customers}", name="update_customer", methods={"PATCH"})
    * @ParamConverter("customers", class="App\Entity\Customer")
    */
    public function updateCustomerAction($customers, Request $request, CustomerService $customerService){
        $result = $customerService->update($customers, $request);
        return new JsonResponse($result);
    }

    /**
    * @SWG\Delete(
    *     @SWG\Parameter(
    *         name="id",
    *         description="ID of customer that needs to be deleted",
    *         required=true,
    *         in="path",
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="Returned when successful",
    *         @Model(type=App\Entity\Customer::class)
    *     ),
    *     @SWG\Response(response="400",description="Returned when the data is missing or data is not correct"),
    *     @SWG\Response(response="500",description="Returned when server side error occurred")
    * )
    * @Route("/customers/{customer}", name="delete_customer", methods={"DELETE"})
    * @ParamConverter("customer", class="App\Entity\Customer")
    */
    public function deleteOrderAction($customer,  CustomerService $customerService){
        $result = $customerService->delete($customer);
        return new JsonResponse($result);
    }

    /**
     * @Route("/soap")
     */
    public function soapAction(CustomerService $customerService)
    {
        $soapServer = new \SoapServer('http://fatcat/fatcat.wsdl', $customerService->getCustomersOrders());
//        $customers = $customerService->getCustomersOrders();
//        foreach ($customers as $customer){
//            $soapServer->setObject($customers);
//        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }

}