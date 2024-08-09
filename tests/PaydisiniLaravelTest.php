<?php

namespace Jstalinko\PaydisiniLaravel\Tests;

use Jstalinko\PaydisiniLaravel\PaydisiniLaravel;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Config;

class PaydisiniLaravelTest extends TestCase
{
    protected $paydisini;

    protected function setUp(): void
    {
        // Mock the configuration values
        Config::shouldReceive('get')
            ->with('paydisini-laravel.api_key')
            ->andReturn('test_api_key');
        
        Config::shouldReceive('get')
            ->with('paydisini-laravel.api_endpoint')
            ->andReturn('https://api.paydisini.co.id/v1/');

        // Mock the Guzzle HTTP client with a response containing "success": true
        $mock = new MockHandler([
            new Response(200, [], '{"success": true}'), // Mock response for success
            new Response(200, [], '{"success": true}'), // Add more responses as needed
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Inject the mocked client into the PaydisiniLaravel class
        $this->paydisini = new PaydisiniLaravel();

        // Overwrite the client to use the mocked one
        $this->paydisini->client = $client;
    }

    public function testGetPaymentChannels()
    {
        $response = $this->paydisini->getPaymentChannels();

        $this->assertJson($response);
        $this->assertStringContainsString('"success": true', $response);
    }

    public function testGetPaymentGuide()
    {
        $response = $this->paydisini->getPaymentGuide('service_code');
        $this->assertJson($response);
        $this->assertStringContainsString('"success": true', $response);
    }

    public function testGetProfile()
    {
        $response = $this->paydisini->getProfile();
        $this->assertJson($response);
        $this->assertStringContainsString('"success": true', $response);
    }

    public function testCreateTransaction()
    {
        $data = [
            'unique_code' => 'INV123',
            'service' => 'service_code',
            'amount' => 100000,
            'customer_email' => 'customer@example.com',
            'note' => 'Test transaction',
            'return_url' => 'https://example.com/return',
            'ewallet_phone' => '081234567890'
        ];

        $response = $this->paydisini->createTransaction($data);
        $this->assertJson($response);
        $this->assertStringContainsString('"success": true', $response);
    }

    public function testDetailTransaction()
    {
        $response = $this->paydisini->detailTransaction('INV123');
        $this->assertJson($response);
        $this->assertStringContainsString('"success": true', $response);
    }

    public function testCancelTransaction()
    {
        $response = $this->paydisini->cancelTransaction('INV123');
        $this->assertJson($response);
        $this->assertStringContainsString('"success": true', $response);
    }
}
