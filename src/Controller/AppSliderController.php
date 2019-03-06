<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class AppSliderController extends AbstractController
{

    const URL = "https://shopify-app-tule.herokuapp.com";
    const ApiKey = "2a5fd5fb378d4dc66a675342ee712c56";
    const ApiSecret = "ebf6f972200bcaeaefd7033fb064f297";

    public function install(Request $request)
    {
        $shop = "https://" . $request->get('shop');
        $scopes = "read_themes,write_themes";
        $url = $shop . "/admin/oauth/request_grant?client_id=" . self::ApiKey . "&scope=" . $scopes . "&redirect_uri=" . self::URL . "/slider/auth";
        return new RedirectResponse($url);
    }


    public function auth(Request $request)
    {

        $code = $request->get('code');
        $shop = $request->get('shop');

        /****************************************** HMAC verify *************************************/
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
        $ver_hmac = hash_hmac('sha256', $str, self::ApiSecret, false);

        if ($ver_hmac != $hmac) {
            return new Response('Something wrong', 500);
        }

        $data = [
            'client_id' => self::ApiKey,
            'client_secret' => self::ApiSecret,
            'code' => $code
        ];

        $client = new Client(['base_uri' => 'https://' . $shop]);
        $response = $client->request('POST', '/admin/oauth/access_token', ['form_params' => $data]);

        $result = (array)json_decode($response->getBody()->getContents());


        if(empty($result['access_token'])) {
            return new Response('Something wrong', 500);
        }

        $token = $result['access_token'];

        $result = $client->request('GET', '/admin/themes.json', [
            'headers' => [
                'X-Shopify-Access-Token' => $token
            ]
        ]);

        $themes = json_decode($result->getBody()->getContents(), true);
        if(!count($themes['themes'])) {
            return new Response('Something wrong', 500);
        }

        $themeId = $themes['themes'][0]['id'];

        $result = $client->request(
            'GET',
            sprintf('admin/themes/%s/assets.json?asset[key]=templates/product.liquid', $themeId),
            [
                'headers' => [
                    'X-Shopify-Access-Token' => $token
                ]
            ]
        );

        $content = json_decode($result->getBody()->getContents(), true);

        $template = $content['asset']['value'];


        $newTemplate = $template.'{% comment %}
  Start  Product Shopmacher Slider
{% endcomment %}
<div id="shopmacher-slider">
       {% for collection in product.collections %}
          {% for product in collection.products %}
            <p>{{ product.title }}</p>
          {% endfor %}
       {% endfor %}
</div>
{% comment %}
  End  Product Shopmacher Slider
{% endcomment %}';


        $client->request(
            'PUT',
            sprintf('admin/themes/%s/assets.json', $themeId),
            [
                'headers' => [
                    'X-Shopify-Access-Token' => $token
                ],
                'form_params' => [
                    'asset' => [
                        'key' => 'templates/product.liquid',
                        'value' => $newTemplate
                    ]
                ]
            ]
        );

        return new Response('Install successfully,  <a href="https://'.$shop.'">back to your shop</a>');
    }
}
