<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppSliderController extends AbstractController
{

    const URL = "https://shopify-app-tule.herokuapp.com/";
    const ApiKey = "2a5fd5fb378d4dc66a675342ee712c56";

    public function install(Request $request)
    {
        $shop = "https://".$request->get('shop');
        $scopes = "read_orders,read_products,write_products";
        $url = $shop."/admin/oauth/request_grant?client_id=".self::ApiKey."&scope=".$scopes."&redirect_uri=".self::URL."/slider/auth";
        return new RedirectResponse($url);
    }


    public function auth()
    {

    }
}
