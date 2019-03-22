<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ShopRepository;
use App\Library\Shopify\ShopifyUtil;
use App\Library\Shopify\ShopifyAuth;
use App\Service\Slider;
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
            $shop->setInstalledDate(new \DateTime());
            $shop->setScriptTagId($slider->addScript());
            $shop->setUpdatedDate(new \DateTime());

            $em->persist($shop);
            $em->flush();

            return new RedirectResponse(sprintf('%s/admin/apps/%s', ShopifyUtil::getShopUrl($_GET['shop']), 'shopiapp_product_slider-1'));
        }

        return $this->render('error/500.html.twig');
    }

    public function config(Request $request)
    {
        if ($request->getMethod() == 'GET') {
            return $this->render('slider/config.html.twig');
        }

    }

    public function uninstall(Slider $slider, Request $request)
    {
        $shopDomain = $request->headers->get('X-Shopify-Shop-Domain');
        $shop = $this->getDoctrine()->getRepository(ShopRepository::class)->findOneBy(['shop_id' => $shopDomain]);
        $slider->removeScript($shop->getScriptTagId());
        return new Response('');
    }
}
