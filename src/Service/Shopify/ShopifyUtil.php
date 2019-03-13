<?php

namespace App\Service\Shopify;

class ShopifyUtil
{
    public static function getShopURL()
    {
        return $_GET['shop'];
    }
}
