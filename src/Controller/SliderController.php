<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Slider;
use App\Library\Shopify\ShopifyUtil;
use App\Library\Shopify\ShopifyAuth;
use App\Entity\Shop;


/**
 * Class SliderController
 * @package App\Controller
 */
class SliderController extends AbstractController
{

    public function install()
    {
        return $this->render('slider/config.html.twig', [
            'apiKey' => getenv('API_KEY'),
            'appUrl' => getenv('APP_URL'),
            'scopes' => 'read_themes,write_themes,write_script_tags,read_products',
            'shopOrigin' => ShopifyUtil::getShopUrl($_GET['shop'])
        ]);
    }


    public function auth(Slider $slider, EntityManagerInterface $em)
    {
        if (ShopifyAuth::validateHMAC()) {

            $accessToken = ShopifyAuth::generateAccessToken();

            $slider->getRequest()->setAccessToken($accessToken);

            $slider->uninstallListen();

            $shop = new Shop();
            $shop->setShopId($_GET['shop']);
            $shop->setAccessToken($accessToken);
            $em->persist($em);
            $em->flush();

            return new RedirectResponse(sprintf('%s/admin/apps/%s', ShopifyUtil::getShopUrl($_GET['shop']), 'shopiapp_product_slider-1'));
        }

        return $this->render('error/500.html.twig');
    }

    public function config(Request $request)
    {
        if ($request->getMethod() == 'GET') {

        }
        return $this->render('slider/config.html.twig');
    }

    public function uninstall(Request $request)
    {

    }
}
