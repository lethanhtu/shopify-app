<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppSliderController extends AbstractController
{

    const URL = "https://shopify-app-tule.herokuapp.com";
    const ApiKey = "2a5fd5fb378d4dc66a675342ee712c56";
    const ApiSecret = "ebf6f972200bcaeaefd7033fb064f297";

    public function install(Request $request)
    {
        $shop = "https://".$request->get('shop');
        $scopes = "read_orders,read_products,write_products";
        $url = $shop."/admin/oauth/request_grant?client_id=".self::ApiKey."&scope=".$scopes."&redirect_uri=".self::URL."/slider/auth";
        return new RedirectResponse($url);
    }


    public function auth(Request $request)
    {
        $hmac = $_GET['hmac'];
        unset($_GET['hmac']);

        foreach($_GET as $key=>$value){

            $key=str_replace("%","%25",$key);
            $key=str_replace("&","%26",$key);
            $key=str_replace("=","%3D",$key);
            $value=str_replace("%","%25",$value);
            $value=str_replace("&","%26",$value);

            $ar[] = $key."=".$value;
        }

        $str = join('&',$ar);
        $ver_hmac =  hash_hmac('sha256',$str,self::ApiSecret,false);

        if($ver_hmac==$hmac)
        {
            echo 'hmac verified';
        } else {
            echo 'fail';
        }
    }
}
