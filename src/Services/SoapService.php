<?php


namespace App\Services;

use App\Repository\CustomerRepository;
use phpDocumentor\Reflection\Types\Mixed_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Customer;


class SoapService
{
    /**
     * CustomerService constructor.
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->repository = $customerRepository;
    }

    /**
     * @return array
     */
    public function getCustomersOrders()
    {
        $customers = $this->repository->findAll();
//        $customers_arr = [];
//        foreach($customers as $customer){
//            array_push($customers_arr, $customer);
////            $orders = $customer->getOrders();
////            $name = $customer->getFirstName() . $customer->getLastName();
////            $orders_arr = [$name => []];
////            foreach ($orders as $order) {
////                array_push($orders_arr[$name], $order);
////            }
////            array_push($customers_arr, $orders_arr);
//        }

        return $customers;
    }
}