<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Shopify\ShopifyUtil;
use App\Service\Shopify\ShopifyAuth;


class SliderController extends AbstractController
{
    public function install()
    {
        $shopUrl = ShopifyUtil::getShopURL();
        $scopes = "read_themes,write_themes";

        $url = sprintf(
            '%s/admin/oauth/request_grant?client_id=%s&scope=%s&redirect_uri=%s/slider/auth',
            $shopUrl,
            getenv('API_KEY'),
            $scopes,
            getenv('APP_URL')
        );

        return new RedirectResponse($url);
    }


    public function auth()
    {
        if(ShopifyAuth::validateHMAC()) {
            return new Redirect('/slider/config');
        }

        return $this->render('error/500.html.twig');
    }

    public function config()
    {
        return $this->render('slider/config.html.twig');
    }
}
