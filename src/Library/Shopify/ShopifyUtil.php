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
        return $_GET['shop'];
        //return sprintf('https://%s', $_GET['shop']);
    }
}
