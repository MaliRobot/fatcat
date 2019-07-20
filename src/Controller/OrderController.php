<?php


namespace App\Controller;

use App\Entity\Order;
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
use App\Services\OrderService;


/**
 * @Route("/api")
 */
class OrderController extends AbstractController
{
    /**
    * @SWG\Get(
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Order::class)
    *     ),
    *     @SWG\Response(
    *         response=404,
    *         description="Returns an error when no orders were found"
    *     )
    * )
    * @Route("/orders", name="orders", methods={"GET", "HEAD"})
    */
    public function getOrderAction(OrderService $orderService){
        $orders = $orderService->getOrders();
        return new JsonResponse($orders);
    }

    /**
    * @SWG\Get(
    *     @SWG\Parameter(
    *         name="id",
    *         description="ID of order that needs to be fetched",
    *         required=true,
    *         in="path",
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response=200,
    *         description="Returns all orders"
    *     ),
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Order::class)
    *     ),
    *     @SWG\Response(
    *         response=404,
    *         description="Returns an error when no orders were found"
    *     )
    * )
    * @Route("/orders/{id}", name="single_order", methods={"GET", "HEAD"})
    */
    public function getSingleOrderAction($id, OrderService $ordersService){
        $order = $ordersService->getSingleOrder($id);
        return new JsonResponse($order);
    }

    /**
    * @SWG\Post(
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Order::class)
    *     ),
    *     @SWG\Parameter(
    *         name="request",
    *         description="New order data",
    *         required=true,
    *         in="body",
    *         type="object",
    *         @SWG\Property(property="order_amount", type="integer"),
    *         @SWG\Property(property="shipping_amount", type="integer"),
    *         @SWG\Property(property="tax_amount", type="integer"),
    *     ),
    *     @SWG\Response(
    *         response="200",
    *             description="Returned when successful",
    *             @Model(type=App\Entity\Order::class)
    *         ),
    *     @SWG\Response(response="400",description="Returned when the data is missing or data is not correct"),
    *     @SWG\Response(response="500",description="Returned when server side error occurred")
    * )
    * @Route("/orders", name="create_order", methods={"POST"})
    */
    public function createOrderAction(Request $request, OrderService $orderService){
        $result = $orderService->create($request);
        return new JsonResponse($result);
    }

    /**
    * @SWG\Patch(
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\Order::class)
    *     ),
    *     @SWG\Parameter(
    *         name="request",
    *         description="New order data",
    *         required=true,
    *         in="body",
    *         type="object",
    *         @SWG\Property(property="orderAmount", type="integer"),
    *         @SWG\Property(property="shippingAmount", type="integer"),
    *         @SWG\Property(property="taxAmount", type="integer"),
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="Returned when successful",
    *         @Model(type=App\Entity\Order::class)
    *     ),
    *     @SWG\Response(response="400",description="Returned when the data is missing or data is not correct"),
    *     @SWG\Response(response="500",description="Returned when server side error occurred")
    * )
    * @Route("/orders/{id}", name="update_order", methods={"PATCH"})
    */
    public function updateOrderAction($id, Request $request, OrderService $orderService){
        $result = $orderService->update($id, $request);
        return new JsonResponse($result);
    }

    /**
    * @SWG\Delete(
    *     @SWG\Parameter(
    *         name="id",
    *         description="ID of order that needs to be fetched",
    *         required=true,
    *         in="path",
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="Returned when successful",
    *         @Model(type=App\Entity\Order::class)
    *     ),
    *     @SWG\Response(response="400",description="Returned when the data is missing or data is not correct"),
    *     @SWG\Response(response="500",description="Returned when server side error occurred")
    * )
    * @Route("/orders/{id}", name="delete_order", methods={"DELETE"})
    */
    public function deleteOrderAction($id, OrderService $orderService){
        $result = $orderService->delete($id);
        return new JsonResponse($result);
    }

    /**
     * @SWG\Get(
     *     @SWG\Parameter(
     *         name="username",
     *         description="id of customer",
     *         required=true,
     *         in="path",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns all customer orders"
     *     ),
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=App\Entity\Order::class)
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returns an error when no orders were found"
     *     )
     * )
     * @Route("/orders/user/{username}", name="get_orders_by_customer", methods={"GET", "HEAD"})
     */
    public function getOrdersByCustomer($customer, OrderService $orderService, SecurityService $securityService){
        $orders = $orderService->getByCustomer($customer);
        return new JsonResponse($orders);
    }

}