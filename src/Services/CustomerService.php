<?php


namespace App\Services;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use phpDocumentor\Reflection\Types\Mixed_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Customer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CustomerService
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
     * @param Request $request
     * @return array
     */
    public function getCustomers(Request $request): array
    {
        $customers = $this->repository->findAll();
        if ($customers != null) {
            $customers_array = [];
            foreach($customers as $customer){
                array_push($customers_array, $this->prepareCustomer($customer));
            }
            return $customers_array;
        }
        return [];
    }

    /**
     * @param Customer $customer
     * @return array
     */
    public function getSingleCustomer(Customer $customer): array
    {
        if ($customer != null) {
            return $this->prepareCustomer($customer);
        }
        return ['error' => 'no such customer'];
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
            $customer = new Customer();
            $content = json_decode($request->getContent(), true);
            $customer = $this::addAttributes($content, $customer);
            $this->repository->save($customer);
            return ['created' => $customer->getId()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return array
     */
    public function update($customer, Request $request): array
    {
        try {
            if ($customer == null) {
                return ['error' => 'there is no customer with that id'];
            }
            $content = json_decode($request->getContent(), true);
            $customer = $this::addAttributes($content, $customer);
            $this->repository->save($customer);
            return ['updated' => $customer->getId()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param $customer
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($customer): array
    {
        try {
            if ($customer == null) {
                return ['error' => 'there is no customer with that id'];
            }
            $this->repository->delete($customer);
            return ['deleted' => $customer->getId()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param Customer $customer
     * @return mixed|string
     */
    static function prepareCustomer(Customer $customer){
        return [
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'country' => $customer->getCountry(),
            'street' => $customer->getStreet()
        ];
    }

    /**
     * @param array $content
     * @param Customer $customer
     * @return Customer
     */
    static function addAttributes(array $content, Customer $customer): Customer
    {
        if (isset($content['first_name'])) {
            $customer->setFirstName($content['first_name']);
        }

        if (isset($content['last_name'])) {
            $customer->setLastName($content['last_name']);
        }

        if (isset($content['email'])) {
            $customer->setEmail($content['email']);
        }

        if (isset($content['country'])) {
            $customer->setCountry($content['country']);
        }

        if (isset($content['street'])) {
            $customer->setStreet($content['street']);
        }
        return $customer;
    }

}