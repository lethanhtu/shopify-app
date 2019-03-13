<?php

namespace App\Service;

use App\Library\Shopify\ShopifyRequest;
use Twig\Environment;

/**
 * Class Slider
 * @package App\Service
 */
class Slider
{
    protected $request;
    protected $twig;

    /**
     * Slider constructor.
     * @param ShopifyRequest $request
     * @param Environment $twig
     */
    public function __construct(ShopifyRequest $request, Environment $twig)
    {
        $this->request = $request;
        $this->twig = $twig;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addContent()
    {
        $this->request->addScriptTag(sprintf('%s/slider/js/slider.js', getenv('APP_URL')));
    }

    public function uninstallListen()
    {
        $this->request->registerWebhook('app\/uninstalled', sprintf('%s/slider/uninstall', getenv('APP_URL')));
    }
}
