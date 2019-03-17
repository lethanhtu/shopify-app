<?php

namespace App\Library\Shopify;

use GuzzleHttp\Client;

class ShopifyAuth
{

    public static function validateRequest()
    {
        if(empty($_GET['shop']) && empty($_GET['hmac']) && empty($_GET['code'])) {
            return new \Exception('Missing information, "shop,hmac,code" are required in url');
        }

        self::validateHMAC();

    }

    /**
     * @return bool
     */
    public static function validateHMAC()
    {
        $hmac = $_GET['hmac'];
        unset($_GET['hmac']);

        foreach ($_GET as $key => $value) {

            $key = str_replace("%", "%25", $key);
            $key = str_replace("&", "%26", $key);
            $key = str_replace("=", "%3D", $key);
            $value = str_replace("%", "%25", $value);
            $value = str_replace("&", "%26", $value);

            $ar[] = $key . "=" . $value;
        }

        $str = join('&', $ar);
        $ver_hmac = hash_hmac('sha256', $str, getenv('API_SECRET'), false);

        return $ver_hmac == $hmac;
    }

    public static function generateAccessToken()
    {

        $client = new Client(['base_uri' => ShopifyUtil::getShopURL($_GET['shop'])]);
        $response = $client->request('POST', '/admin/oauth/access_token', ['form_params' =>
            [
                'client_id' => getenv('API_KEY'),
                'client_secret' => getenv('API_SECRET'),
                'code' => $_GET['code']
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);


        if(empty($result['access_token'])) {
            return new \Exception('Access Token is empty');
        }



        return $result['access_token'];
    }
}
