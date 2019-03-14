<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Slider;
use App\Library\Shopify\ShopifyUtil;
use App\Library\Shopify\ShopifyAuth;

/**
 * Class SliderController
 * @package App\Controller
 */
class SliderController extends AbstractController
{
    public function install()
    {
        return $this->render('base.html.twig', [
            'apiKey' => getenv('API_KEY'),
            'appUrl' => getenv('APP_URL'),
            'scopes' => 'read_themes,write_themes,write_script_tags',
            'shopOrigin' => ShopifyUtil::getShopURL()
        ]);
    }


    public function auth(Slider $slider)
    {
        if(ShopifyAuth::validateHMAC()) {
            $slider->uninstallListen();
            $slider->addContent();
            return new RedirectResponse('%s/admin/apps/%s', ShopifyUtil::getShopURL(), 'shopiapp_product_slider-1');
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

    }
}
