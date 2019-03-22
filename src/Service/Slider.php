<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Library\Shopify\ShopifyRequest;
use App\Entity\Shop;
use Twig\Environment;

/**
 * Class Slider
 * @package App\Service
 */
class Slider
{
    protected $request;
    protected $twig;
    protected $entityManager;

    /**
     * Slider constructor.
     * @param ShopifyRequest $request
     * @param Environment $twig
     */
    public function __construct(ShopifyRequest $request, Environment $twig, EntityManagerInterface $em)
    {
        $this->request = $request;
        $this->twig = $twig;
        $this->em = $em;
    }


    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addScript()
    {
        $response = $this->request->addScriptTag(sprintf('%s/slider/js/slider.js', getenv('APP_URL')));
        $result = json_decode($response->getBody()->getContents(), true);
        if(!isset($result['script_tag']) || !isset($result['script_tag']['id'])) {
            throw new Exception('Script tag id is missed');
        }

        return $result['script_tag']['id'];

    }

    public function removeScript($shopDomain)
    {
        $shop = $this->em->getRepository(Shop::class)->findOneBy(['shop_id' => $shopDomain]);
        $this->request->setAccessToken($shop->getAccessToken());
        $this->request->deleteScriptTag($shop->getScriptTagId());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uninstallListen()
    {
        $this->request->registerWebhook('app/uninstalled', sprintf('%s/slider/uninstall', getenv('APP_URL')));
    }


    /**
     * @return ShopifyRequest
     */
    public function getRequest()
    {
        return $this->request;
    }
}
