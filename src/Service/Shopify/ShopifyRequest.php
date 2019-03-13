<?php

namespace App\Service\Shopify;

use GuzzleHttp\Client;

class ShopifyRequest
{
    protected $accessToken;
    protected $shop;

    public function __construct()
    {
        $this->accessToken  = ShopifyAuth::validateHMAC();
    }

    public function updateTemplate()
    {

    }
}
