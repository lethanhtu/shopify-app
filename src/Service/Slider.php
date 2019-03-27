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
     * @param $sliderId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addContent($sliderId)
    {
        $indexContent = $this->request->getTemplateContent('templates/index.liquid');
        $indexContent.= sprintf(
            '<div id="shopmacher-slider" slider-id="%s"><script src="%s/slider/embbed.js" defer></script></div>',
            $sliderId,
            getenv('APP_URL')
        );

        $this->request->updateTemplateContent('templates/index.liquid', $indexContent);
    }

    public function uninstall($shopDomain)
    {
        $shop = $this->em->getRepository(Shop::class)->findOneBy(['shop_id' => $shopDomain]);
        $shop->setActive(0);
        $shop->setUpdatedDate(new \DateTime());
        $this->em->persist($shop);
        $this->em->flush();
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
