<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;


class runDemoCommand extends Command
{
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->token = NULL;
        $this->dotenv = new Dotenv();
        $this->dotenv->load($this->path . '/.env');
        $this->baseURL = 'http://' . $_ENV['URL'];
        parent::__construct();
    }

    protected static $defaultName = 'app:run';

    protected function configure()
    {
        // ...
    }

    protected function getToken($httpClient) {

        $response = $httpClient->request('POST', $this->baseURL . '/login', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body'   => json_encode([
                'username' => 'zika',
                'password' => 'alas'
            ])
        ]);

        if ($response->getStatusCode() != 200){

        } else {
            $token = json_decode($response->getContent(), true)['token'];
            $this->token = $token;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting test runs of API routes');

        $httpClient = HttpClient::create();

        if ($this->token == NULL) {
            $this->getToken($httpClient);
        }

        $output->writeln('Creating (POST) Customer entity');

        $response = $httpClient->request('POST', $this->baseURL . '/api/customers', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                "first_name" => "mika",
                "last_name" => "alas",
                "email" => "mika@alas.com",
                "street" => "Belgrade Strasse 1",
                "country" => "Serbia"
            ])
        ]);

        $customerId = json_decode($response->getContent(), true)['created'];
        $output->writeln($customerId . ' - we have id so it means it worked');

        $response = $httpClient->request('GET', $this->baseURL . '/api/customers/' . $customerId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $output->writeln('If call was successful we should see object attributes bellow');
        dump(json_decode($response->getContent(), true));

        $output->writeln('Testing customer update');
        $response = $httpClient->request('PATCH', $this->baseURL . '/api/customers/' . $customerId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                "country" => "Columbia"
            ])
        ]);

        $customerUpdated = json_decode($response->getContent(), true)["updated"];
        if ($customerUpdated == $customerId) {
            $output->writeln('Result: ' . (string) $customerUpdated == $customerId);
            $output->writeln('We compared returned ID with original one, means the method worked ok');
        }

        $output->writeln('Testing delete method on Customer');
        $response = $httpClient->request('DELETE', $this->baseURL . '/api/customers/' . $customerId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $customerUpdated = json_decode($response->getContent(), true)["deleted"];
        if ($customerUpdated == $customerId) {
            $output->writeln('Result: ' . (string) $customerUpdated == $customerId);
            $output->writeln('This time matched ID means Customer is deleted successfully');
        }

        $output->writeln('Testing creating Order');
        $response = $httpClient->request('POST', $this->baseURL . '/api/orders', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                "order_amount" => "23",
                "shipping_amount" => "21",
                "tax_amount" => "4",
                "customer_id" => "12"
            ])
        ]);

        $orderId = json_decode($response->getContent(), true)['created'];
        $output->writeln("Deleted key: " . $orderId);
        $output->writeln('If script successfully ran it means it found created key in response
        which is excatly what we need as a proof that the method works');

        $output->writeln('Checking get method with Order');
        $response = $httpClient->request('GET', $this->baseURL . '/api/orders/' . $orderId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        dump(json_decode($response->getContent(), true));
        $output->writeln('If everything is ok if previous lined printed order attributes.');

        $output->writeln('Testing patch method on Order');
        $response = $httpClient->request('PATCH', $this->baseURL . '/api/orders/' . $orderId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ],
            'body' => json_encode([
                "orderAmount" => "33"
            ])
        ]);

        $orderUpdated = json_decode($response->getContent(), true)["updated"];
        if ($orderUpdated == $orderId) {
            $output->writeln('Here we compared returned ID with original one,' .
            'equal means the method worked ok');
        }

        $output->writeln('Testing delete method on order');
        $response = $httpClient->request('DELETE', $this->baseURL . '/api/orders/' . $orderId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $orderUpdated = json_decode($response->getContent(), true)["deleted"];
        if ($orderUpdated == $orderId) {
            $output->writeln('Again, we compared returned ID with original one, and equal means it is ok');
        }

        $output->writeln('Testing SOAP method');
        $response = $httpClient->request('GET', $this->baseURL . '/soap', [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $output->writeln('Finish');

    }
}