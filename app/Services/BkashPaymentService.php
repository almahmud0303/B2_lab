<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashPaymentService
{
    private $appKey;
    private $appSecret;
    private $username;
    private $password;
    private $baseUrl;
    private $token;
    private $tokenExpiresAt;

    public function __construct()
    {
        $this->appKey = config('bkash.app_key');
        $this->appSecret = config('bkash.app_secret');
        $this->username = config('bkash.username');
        $this->password = config('bkash.password');
        $this->baseUrl = config('bkash.base_url', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
    }

    /**
     * Get access token for bKash API
     */
    public function getToken()
    {
        try {
            // Check if we already have a valid token
            if ($this->token && $this->isTokenValid()) {
                return $this->token;
            }

            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $this->username,
                'password' => $this->password,
            ])->post($this->baseUrl . '/tokenized/checkout/token/grant', [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['id_token']) && $data['statusCode'] === '0000') {
                    $this->token = $data['id_token'];
                    $this->tokenExpiresAt = now()->addMinutes(50); // bKash tokens expire in 1 hour
                    return $this->token;
                }
            }

            Log::error('bKash token failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('bKash token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if the current token is still valid
     */
    private function isTokenValid()
    {
        return isset($this->tokenExpiresAt) && $this->tokenExpiresAt->isFuture();
    }


    /**
     * Create payment request
     */
    public function createPayment($amount, $invoiceNumber, $callbackUrl = null)
    {
        // Log payment creation attempt
        Log::info('bKash payment creation started', [
            'amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'callback_url' => $callbackUrl
        ]);

        // For testing purposes, redirect to a test page that simulates bKash
        if (config('bkash.sandbox', true)) {
            // Create a test payment URL that redirects to our test page
            $testPaymentId = 'TEST_' . strtoupper(uniqid());
            $testBkashUrl = url('/payment/bkash-test/' . $testPaymentId);
            
            Log::info('Using test bKash URL for sandbox', [
                'payment_id' => $testPaymentId,
                'bkash_url' => $testBkashUrl
            ]);
            
            return [
                'success' => true,
                'paymentID' => $testPaymentId,
                'bkashURL' => $testBkashUrl,
                'callbackURL' => $callbackUrl ?: url('/payment/callback'),
            ];
        }

        if (!$this->token) {
            $this->getToken();
        }

        if (!$this->token) {
            Log::error('bKash token not available');
            return ['error' => 'Unable to get bKash token'];
        }

        try {
            $paymentData = [
                'mode' => '0011', // Test mode for sandbox
                'payerReference' => 'Student_' . auth()->id(),
                'callbackURL' => $callbackUrl ?: url('/payment/callback'),
                'amount' => number_format($amount, 2, '.', ''), // Ensure proper decimal format
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $invoiceNumber,
            ];

            Log::info('bKash payment data prepared', $paymentData);

            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/payment/create', $paymentData);

            Log::info('bKash API response received', [
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['statusCode']) && $data['statusCode'] === '0000') {
                    Log::info('bKash payment created successfully', [
                        'payment_id' => $data['paymentID'],
                        'bkash_url' => $data['bkashURL']
                    ]);
                    
                    return [
                        'success' => true,
                        'paymentID' => $data['paymentID'],
                        'bkashURL' => $data['bkashURL'],
                        'callbackURL' => $data['callbackURL'],
                    ];
                } else {
                    $errorMessage = $data['statusMessage'] ?? 'Payment creation failed';
                    Log::error('bKash payment creation failed', [
                        'status_code' => $data['statusCode'] ?? 'unknown',
                        'status_message' => $errorMessage,
                        'response_data' => $data
                    ]);
                    return ['error' => $errorMessage];
                }
            }

            Log::error('bKash payment creation HTTP error', [
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);
            return ['error' => 'Payment creation failed - HTTP ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('bKash payment creation error: ' . $e->getMessage());
            return ['error' => 'Payment creation error'];
        }
    }

    /**
     * Execute payment after user completes payment
     */
    public function executePayment($paymentID)
    {
        if (!$this->token) {
            $this->getToken();
        }

        if (!$this->token) {
            return ['error' => 'Unable to get bKash token'];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/payment/execute/' . $paymentID);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => $data['statusCode'] === '0000',
                    'statusCode' => $data['statusCode'],
                    'statusMessage' => $data['statusMessage'],
                    'transactionID' => $data['trxID'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'paymentID' => $data['paymentID'] ?? null,
                ];
            }

            Log::error('bKash payment execution failed: ' . $response->body());
            return ['error' => 'Payment execution failed'];
        } catch (\Exception $e) {
            Log::error('bKash payment execution error: ' . $e->getMessage());
            return ['error' => 'Payment execution error'];
        }
    }

    /**
     * Query payment status
     */
    public function queryPayment($paymentID)
    {
        if (!$this->token) {
            $this->getToken();
        }

        if (!$this->token) {
            return ['error' => 'Unable to get bKash token'];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->token,
                'X-APP-Key' => $this->appKey,
            ])->get($this->baseUrl . '/tokenized/checkout/payment/query/' . $paymentID);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => $data['statusCode'] === '0000',
                    'statusCode' => $data['statusCode'],
                    'statusMessage' => $data['statusMessage'],
                    'transactionID' => $data['trxID'] ?? null,
                    'amount' => $data['amount'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'paymentID' => $data['paymentID'] ?? null,
                ];
            }

            Log::error('bKash payment query failed: ' . $response->body());
            return ['error' => 'Payment query failed'];
        } catch (\Exception $e) {
            Log::error('bKash payment query error: ' . $e->getMessage());
            return ['error' => 'Payment query error'];
        }
    }
}
