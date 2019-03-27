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
        $productContent = $this->request->getTemplateContent('templates/product.liquid');
        $productContent.= sprintf(
            '{% comment %}
  Start  Product Shopmacher Slider
{% endcomment %}<div id="shopmacher-slider" slider-id="%s"><script src="%s/slider/embed.js" defer></script></div>',
            $sliderId,
            getenv('APP_URL')
        );

        $productContent.='
<div id="shopmacher-slider-content" class="recommendation-items-block" style="display:none">
  <div class="page-width">
    <h2 class="heading">
      DIE BESTSELLER
    </h2>
      {% assign exist_id = "" %}
    <div class="recommendation-items">
        {% for collection in product.collections %}
            {% for product in collection.products %}
                {% if exist_id contains product.id %}
                {% else %}
                  <div class="item">
                    <a href="{{ product.url }}">
              <span class="thumb">
                <img src="{{ product | img_url }}"/>
              </span>
                      <span class="content">
                <div class="name-pd"></div>
                <div class="short-desc"></div>
                <div class="price-pd">{{product.price}} $</div>
              </span>
                    </a>
                  </div>
                    {% assign exist_id = exist_id | append: product.id %}
                {% endif %}
            {% endfor %}
        {% endfor %}
    </div>
  </div>
</div>
{% comment %}
  End  Product Shopmacher Slider
{% endcomment %}';

        $this->request->updateTemplateContent('templates/product.liquid', $productContent);
    }

    public function uninstall($shopDomain)
    {
        $shop = $this->em->getRepository(Shop::class)->findOneBy(['shop_id' => $shopDomain]);
        $shop->setActive(0);
        $shop->setUpdatedDate(new \DateTime());
        $shop->setConfig(null);
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
