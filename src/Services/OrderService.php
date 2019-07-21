<?php


namespace App\Services;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    /**
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository,
                                CustomerRepository $customerRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->repository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->em = $entityManager;
    }

    /**
     * @param $entity
     * @param array $groupArray
     * @return mixed|string
     */
    public function prepareOrder(Order $order): array
    {
        return [
            'order_id' => $order->getId(),
            'order_amount' => $order->getOrderAmount(),
            'shipping_amount' => $order->getShippingAmount(),
            'tax_amount' => $order->getTaxAmount(),
        ];
    }

    /**
     * @return array
     */
    public function getOrders(): array
    {
        $orders = $this->repository->findAll();
        if ($orders != null) {
            $orders_array = [];
            foreach($orders as $order){
                array_push($orders_array, $this->prepareOrder($order));
            }
            return $orders_array;
        }
        return [];
    }

    /**
     * @param $id
     * @return array|mixed|string
     */
    public function getSingleOrder($id): array
    {
        $order = $this->repository->find($id);
        if ($order != null) {
            return $this->prepareOrder($order);
        }
        return [];
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Request $request): array
    {
        try {
            $order = new Order();
            $content = json_decode($request->getContent(), true);
            $order = $this::addAttributes($content, $order);
            $customer = $this->customerRepository->find($content['customer_id']);
            if ($customer == null) {
                return ['error' => 'no customer with that id'];
            }
            $order->setCustomer($customer);
            $this->repository->save($order);
            $customer->addOrder($order);
            $this->customerRepository->save($customer);

            return ['created' => $order->getId()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update($id, Request $request): array
    {
        try {
            $order = $this->repository->find($id);
            if ($order == null) {
                return ['error' => 'there is no order with that id'];
            }
            $content = json_decode($request->getContent(), true);
            $order = $this::addAttributes($content, $order);
            if (array_key_exists('customer', $content)) {
                $customer = $this->customerRepository->find($content['customer']);
                if ($customer != null) {
                    $customer->addOrder($order);
                    $this->customerRepository->save($order);
                }
                $order->setCustomer($customer);
            }
            $this->repository->save($order);
            return ['updated' => $order->getId()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param $customer
     * @return array
     */
    public function getByCustomer($customer): array
    {
        try {
            $orders = $this->repository->findBy(['customer' => $customer]);
            if ($orders == null) {
                return ['error' => 'there are no orders from that customer'];
            }
            return ['orders' => $orders];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param $id
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($id){
        try {
            $order = $this->repository->find($id);
            if ($order == null) {
                return ['error' => 'there is no order with that id'];
            }
            $this->repository->delete($order);
            return ['deleted' => $id];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param array $content
     * @param Order $order
     * @return Order
     */
    static function addAttributes(array $content, Order $order): Order
    {
        if (isset($content['order_amount'])) {
            $order->setOrderAmount($content['order_amount']);
        }

        if (isset($content['shipping_amount'])) {
            $order->setShippingAmount($content['shipping_amount']);
        }

        if (isset($content['tax_amount'])) {
            $order->setTaxAmount($content['tax_amount']);
        }

        return $order;
    }

}