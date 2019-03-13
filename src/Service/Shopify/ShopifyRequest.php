<?php

namespace App\Service\Shopify;



class ShopifyRequest
{
    protected $accessToken;
    protected $shop;

    public function __construct()
    {
        $this->accessToken  = ShopifyAuth::validateHMAC();
    }

    public function getShopUrl()
    {
        return 'https://'.$this->shop;
    }
}
