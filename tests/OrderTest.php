<?php


namespace App\Tests;

use App\Entity\Order;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class OrderTest extends TestCase
{
    public function testCreateArticle(){
        $order = new Order();
        $order->setTaxAmount(20);
        $order->setOrderAmount(200);
        $this->assertEquals(200, $order->getTaxAmount());
        $this->assertEquals(200, $order->getOrderAmount());
    }

    public function testGetOrders(){
        $baseUrl = getenv('TEST_BASE_URL');
        $client = new \GuzzleHttp\Client([
            'base_url' => $baseUrl,
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        $response = $client->request('GET', $baseUrl . '/api/orders');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetOneOrder(){
        $baseUrl = getenv('TEST_BASE_URL');
        $client = new \GuzzleHttp\Client([
            'base_url' => $baseUrl,
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        $response = $client->request('GET', $baseUrl . '/api/order/1');
        $this->assertEquals(200, $response->getStatusCode());
    }
}