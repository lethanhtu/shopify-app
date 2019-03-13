<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use App\Service\Shopify;


class AppSliderController extends AbstractController
{

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


        $newTemplate = $template.'
{% comment %}
  Start  Product Shopmacher Slider
{% endcomment %}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<div id="shopmacher-slider" style="margin-left:50px; margin-right:50px">
  {% assign exist_id = "" %}
  <div class="owl-carousel owl-theme">
   {% for collection in product.collections %}
      {% for product in collection.products %}
        {% if exist_id contains product.id %}
        {% else %}
          <div class="item">
            <a href="{{ product.url }}">
              <img src="{{ product | img_url }}"/>
              <p style="text-align:center; font-weight:bold; margin-top:20px">{{product.price}} $</p>
            </a>
          </div>
          {% assign exist_id = exist_id | append: product.id %}          
        {% endif %}
      {% endfor %}
   {% endfor %}
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" defer="defer"></script>

<script src="https://shopify-app-tule.herokuapp.com/slider.js" defer="defer">
</script>
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
