<?php

namespace App\Library\Shopify;

class ShopifyUtil
{
    /**
     * @param $shopId
     * @return string
     */
    public static function getShopUrl($shopId)
    {
        return sprintf('https://%s', $shopId);
    }
}
