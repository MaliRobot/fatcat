<?php


namespace App\Services;

use App\Repository\CustomerRepository;


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
        return $this->repository->findAll();
    }
}