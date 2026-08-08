<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzService
{
    protected string $storeId;
    protected string $storePassword;
    protected string $apiDomain;

    public function __construct()
    {
        $getGlobalSetting = function($key) {
            $setting = \App\Models\Setting::withoutGlobalScope(\App\Scopes\TenantScope::class)
                ->where('tenant_id', 1)
                ->where('key', $key)
                ->first();
            return $setting ? $setting->value : null;
        };

        $this->storeId = $getGlobalSetting('sslcommerz_store_id') ?: config('sslcommerz.store_id');
        $this->storePassword = $getGlobalSetting('sslcommerz_store_password') ?: config('sslcommerz.store_password');
        
        $isSandbox = $getGlobalSetting('sslcommerz_is_sandbox');
        if ($isSandbox !== null) {
            $isSandbox = filter_var($isSandbox, FILTER_VALIDATE_BOOLEAN);
            $this->apiDomain = $isSandbox 
                ? 'https://sandbox.sslcommerz.com' 
                : 'https://securepay.sslcommerz.com';
        } else {
            $this->apiDomain = config('sslcommerz.api_domain');
        }
    }

    /**
     * Initiate payment and return the gateway URL.
     *
     * @param string $transactionId
     * @param float $amount
     * @param string $planName
     * @param \App\Models\Tenant $tenant
     * @return string|null Gateway URL or null on failure
     */
    public function initiatePayment(string $transactionId, float $amount, string $planName, $tenant): ?string
    {
        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $amount,
            'currency' => 'BDT',
            'tran_id' => $transactionId,
            'success_url' => route('dashboard.billing.payment.success'),
            'fail_url' => route('dashboard.billing.payment.fail'),
            'cancel_url' => route('dashboard.billing.payment.cancel'),
            'ipn_url' => route('dashboard.billing.payment.ipn'),
            
            // Customer Info (Required)
            'cus_name' => $tenant->users->first()->name ?? 'Tenant User',
            'cus_email' => $tenant->users->first()->email ?? 'tenant@example.com',
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_postcode' => '1000',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01711111111',
            
            // Shipping Info (Required by API, we send default since it is SaaS)
            'shipping_method' => 'NO',
            'num_of_item' => 1,
            
            // Product Info
            'product_name' => $planName,
            'product_category' => 'Subscription',
            'product_profile' => 'non-physical-goods',
        ];

        $response = Http::asForm()->post("{$this->apiDomain}/gwprocess/v4/api.php", $postData);

        if ($response->successful() && isset($response['status']) && $response['status'] === 'SUCCESS') {
            return $response['GatewayPageURL'];
        }

        Log::error('SSLCommerz Initiate Payment Failed', ['response' => $response->body()]);
        return null;
    }

    /**
     * Validate the payment by val_id
     *
     * @param string $valId
     * @param float $amount
     * @param string $currency
     * @return array|null Returns validation data on success, null on failure
     */
    public function validatePayment(string $valId, float $amount, string $currency = 'BDT'): ?array
    {
        $response = Http::get("{$this->apiDomain}/validator/api/validationserverAPI.php", [
            'val_id' => $valId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['status']) && ($data['status'] === 'VALID' || $data['status'] === 'VALIDATED')) {
                // Ensure amount and currency match as per SSLCommerz guidelines
                if ($data['currency'] === $currency && floatval($data['amount']) === floatval($amount)) {
                    return $data;
                }
            }
        }

        Log::error('SSLCommerz Payment Validation Failed', ['val_id' => $valId, 'response' => $response->body()]);
        return null;
    }
}
