<?php

namespace Jstalinko\PaydisiniLaravel;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class PaydisiniLaravel
{
    public $client;
    protected $apikey;

    /**
     * Create a new PaydisiniLaravel instance.
     *
     * This constructor initializes the HTTP client and sets the API key
     * from the configuration file.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apikey = config('paydisini-laravel.api_key');
        $this->client = new Client(['base_uri' => 'https://api.paydisini.co.id/v1/' ]);
    }
    
    /**
     * Generate a signature for API requests.
     *
     * This method generates a unique signature by hashing the API key and 
     * the specified method name.
     *
     * @param  string  $method
     * @return string
     */
    public function signature(string $method): string
    {
        return md5($this->apikey.$method);
    }

    /**
     * Retrieve available payment channels.
     *
     * This method sends a request to the Paydisini API to retrieve the 
     * available payment channels.
     *
     * @return Collection
     */
    public function getPaymentChannels(): Collection
    {
        $response = $this->client->request('POST' , '.' , [
            'form_params' => [
                'key' => $this->apikey,
                'request' => 'payment_channel',
                'signature' => $this->signature('PaymentChannel')
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return collect($data);
    }

    /**
     * Retrieve the payment guide for a specific service.
     *
     * This method sends a request to the Paydisini API to get the payment 
     * guide for a specified service.
     *
     * @param  string  $service
     * @return Collection
     */
    public function getPaymentGuide(string $service): Collection
    {
        $response = $this->client->request('POST','.' , [
            'form_params' => [
                'key' => $this->apikey,
                'request' => 'payment_guide',
                'signature' => $this->signature($service.'PaymentGuide')
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return collect($data);
    }

    /**
     * Retrieve the user's profile.
     *
     * This method sends a request to the Paydisini API to retrieve the
     * user's profile details.
     *
     * @return Collection
     */
    public function getProfile(): Collection
    {
        $response = $this->client->request('POST','.' , [
            'form_params' => [
                'key' => $this->apikey,
                'request' => 'profile',
                'signature' => $this->signature('Profile')
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return collect($data);
    }

    /**
     * Create a new transaction.
     *
     * This method sends a request to the Paydisini API to create a new 
     * transaction. It accepts an array of transaction data and generates 
     * the required payload for the request.
     *
     * @param  array  $data
     * @return Collection
     */
    public function createTransaction(array $data): Collection
    {
        
        $payload = [
            'key' => $this->apikey,
            'request' => 'new',
            'unique_code' => $data['unique_code'], // unique_code : like a invoice
            'service' => $data['service'], // service ID : like a payment method code.
            'amount' => $data['amount'],
            'customer_email' => $data['customer_email'],
            'note' => $data['note'], 
            'valid_time' => '10800',
            'type_fee' => '1',
            'payment_guide' => TRUE, // Set TRUE if you want to display payment guide
            'signature' => $this->signature($data['unique_code'].$data['service'].$data['amount'].'10800'.'NewTransaction'),
            'return_url' => $data['return_url']
        ];
        
        if($data['ewallet_phone'] !== '') {
            $payload['ewallet_phone'] = $data['ewallet_phone'];
        }

        $response = $this->client->request('POST' , '.' , [
            'form_params' => $payload
        ]);
        
        $data = json_decode($response->getBody()->getContents(), true);

        return collect($data);
    }

    /**
     * Get the details of a specific transaction.
     *
     * This method sends a request to the Paydisini API to retrieve the 
     * details of a specific transaction based on the unique code.
     *
     * @param  string  $unique_code
     * @return Collection
     */
    public function detailTransaction(string $unique_code): Collection
    {
        $response = $this->client->request('POST' , '.' , [
            'form_params' => [
                'key' => $this->apikey,
                'request' => 'status',
                'unique_code' => $unique_code,
                'signature' => $this->signature($unique_code.'StatusTransaction')
            ]
        ]);
    
        $data = json_decode($response->getBody()->getContents(), true);

        return collect($data);
    }

    /**
     * Cancel a specific transaction.
     *
     * This method sends a request to the Paydisini API to cancel a 
     * specific transaction based on the unique code.
     *
     * @param  string  $unique_code
     * @return Collection
     */
    public function cancelTransaction(string $unique_code): Collection
    {
        $response = $this->client->request('POST' , '.' , [
            'form_params' => [
                'key' => $this->apikey,
                'request' => 'cancel',
                'unique_code' => $unique_code,
                'signature' => $this->signature($unique_code.'CancelTransaction')
            ]
        ]);
    

        $data = json_decode($response->getBody()->getContents(), true);

        return collect($data);
    }

     /**
     * Handle the callback from the payment gateway.
     *
     * @return JsonResponse
     */
    public function handleCallback(Request $request, string $paymentId): JsonResponse
    {
        $key = $request->input('key', '');
        $uniqueCode = $request->input('unique_code', '');
        $status = $request->input('status', '');
        $signature = $request->input('signature', '');

        // Define the expected signature
        $expectedSignature = md5($this->apikey . $paymentId . 'CallbackStatus');

        // Verify the signature and status
        if ($key === $this->apikey && $request->ip() === '84.247.150.90') {
            if ($signature === $expectedSignature) {
                // Process payment status
                if ($status === 'Success') {
                    // Handle successful payment (e.g., update database)
                    // mysqli_query('YOUR QUERY IF PAYMENT SUCCESS');
                    return response()->json(['success' => true]);
                } elseif ($status === 'Canceled') {
                    // Handle canceled payment (e.g., update database)
                    // mysqli_query('YOUR QUERY IF PAYMENT CANCELED');
                    return response()->json(['success' => true]);
                }
            }
        }

        // Return failure response if validation fails
        return response()->json(['success' => false]);
    }
}