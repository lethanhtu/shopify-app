<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Slider;
use App\Library\Shopify\ShopifyUtil;
use App\Library\Shopify\ShopifyAuth;


class SliderController extends AbstractController
{
    public function install()
    {
        file_put_contents('db_webhook', 'aaa', 8);
        $shopUrl = ShopifyUtil::getShopURL();
        $scopes = "read_themes,write_themes,write_script_tags";

        $url = sprintf(
            '%s/admin/oauth/request_grant?client_id=%s&scope=%s&redirect_uri=%s/slider/auth',
            $shopUrl,
            getenv('API_KEY'),
            $scopes,
            getenv('APP_URL')
        );

        return new RedirectResponse($url);
    }


    public function auth(Slider $slider)
    {
        if(ShopifyAuth::validateHMAC()) {
            $slider->uninstallListen();
            $slider->addContent();
            return new Response('ok');
        }

        return $this->render('error/500.html.twig');
    }

    public function config(Request $request)
    {
        if($request->getMethod() == 'GET' ) {

        }
        return $this->render('slider/config.html.twig');
    }

    public function uninstall(Request $request)
    {
        file_put_contents('db_webhook', $request->getContent(), 8);
    }
}
