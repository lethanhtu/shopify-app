<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @return Response
     */
    public function install()
    {
        return $this->render('slider/config.html.twig', [
            'apiKey' => getenv('API_KEY'),
            'appUrl' => getenv('APP_URL'),
            'scopes' => 'read_themes,write_themes,write_script_tags,read_products',
            'shopOrigin' => ShopifyUtil::getShopUrl($_GET['shop'])
        ]);
    }

    /**
     * @param Slider $slider
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function auth(Slider $slider, EntityManagerInterface $em)
    {
        if (!ShopifyAuth::validateHMAC()) {
            return new Response('Invalid hmac signature', 401);
        }


        $accessToken = ShopifyAuth::generateAccessToken();

        $slider->getRequest()->setAccessToken($accessToken);

        $slider->uninstallListen();


        $shop = $em->getRepository(Shop::class)->findOneBy(['shop_id' => $_GET['shop']]);

        if(!$shop) {
            $shop = new Shop();
            $shop->setInstalledDate(new \DateTime());
            $shop->setShopId($_GET['shop']);
        }

        $shop->setActive(1);
        $shop->setAccessToken($accessToken);
        $shop->setUpdatedDate(new \DateTime());

        $em->persist($shop);
        $em->flush();


        $slider->addContent($shop->getId());

        return new RedirectResponse(sprintf('%s/admin/apps/%s', ShopifyUtil::getShopUrl($_GET['shop']), getenv('APP_NAME')));


    }

    /**
     * @param Request $request
     * @return Response
     */
    public function config(Request $request)
    {
        if ($request->getMethod() == 'GET') {
            return $this->render('slider/config.html.twig');
        }
    }

    /**
     * @param Slider $slider
     * @param Request $request
     * @return Response
     */
    public function uninstall(Slider $slider, Request $request)
    {
        $hash_hmac = hash_hmac('sha256', $request->getContent(), getenv('API_SECRET'), true);

        if (!hash_equals(base64_encode($hash_hmac), $request->headers->get('x-shopify-hmac-sha256'))) {
            return new Response('Invalid webhook signature', 401);
        }

        $slider->uninstall($request->headers->get('X-Shopify-Shop-Domain'));
        return new Response('');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function generateJs(Request $request, EntityManagerInterface $em)
    {
        $shop = $em->getRepository(Shop::class)->findOneBy(['id' => $request->get('slider-id'), 'active' => 1]);

        $jsResponse =  new Response('', 200, ['Content-Type' => 'application/javascript']);


        if(!$shop) {
            return $jsResponse;
        }



        return $this->render('slider/js_template.html.twig', [], $jsResponse);
    }
}
