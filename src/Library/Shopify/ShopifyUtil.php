<?php

namespace App\Library\Shopify;

class ShopifyUtil
{
    public static function getShopURL()
    {
        return sprintf('https://%s', $_GET['shop']);
    }
}
