<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Run script which will use every API endpoint exactly once and check correctness
 * Class runDemoCommand
 * @package App\Command
 */
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

    /**
     * @param $httpClient
     */
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting test run of API routes');
        $output->writeln('*******************************');

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
        $output->writeln($customerId . ' - we are able to get id so it means the route worked');
        $output->writeln('*******************************');
        $output->writeln('Testing GET customer route');

        $response = $httpClient->request('GET', $this->baseURL . '/api/customers/' . $customerId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $output->writeln('If call was successful we should see object attributes bellow');
        dump(json_decode($response->getContent(), true));
        $output->writeln('*******************************');

        $output->writeln('Testing Customer PATCH route');
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
            $output->writeln('We compared returned ID with original one, meaning the method worked ok');
        }

        $output->writeln('*******************************');
        $output->writeln('Testing Delete Customer route');
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

        $output->writeln('*******************************');
        $output->writeln('Testing POST Order route');
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
        $output->writeln('If script successfully ran it means it found created key in response
        which is excatly what we need as a proof that the method works');

        $output->writeln('*******************************');
        $output->writeln('Checking GET Order route');
        $response = $httpClient->request('GET', $this->baseURL . '/api/orders/' . $orderId, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        dump(json_decode($response->getContent(), true));
        $output->writeln('If everything is ok if previous lined printed order attributes.');
        $output->writeln('*******************************');

        $output->writeln('Testing PATCH Order route');
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

        $output->writeln('*******************************');
        $output->writeln('Testing Delete Order method');
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

        $output->writeln('*******************************');
        $output->writeln('Testing SOAP method');
        $response = $httpClient->request('GET', $this->baseURL . '/soap', [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $output->writeln('Soap is //TODO :(');
        $output->writeln('Finish');

    }
}