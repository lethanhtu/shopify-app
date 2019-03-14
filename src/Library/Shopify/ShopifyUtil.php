<?php

namespace App\Library\Shopify;

/**
 * Class ShopifyUtil
 * @package App\Library\Shopify
 */
class ShopifyUtil
{
    public static function getShopURL()
    {
        return sprintf('https://%s', $_GET['shop']);
    }
}
