<?php

namespace App\Service\Shopify;

use GuzzleHttp\Client;

/**
 * Class ShopifyRequest
 * @package App\Service\Shopify
 */
class ShopifyRequest
{
    protected $accessToken;
    protected $client;
    protected $themeId;


    public function __construct()
    {
        $this->accessToken = ShopifyAuth::getAccessToken();
        $this->client = new Client(['base_uri' => ShopifyUtil::getShopURL()]);
        $this->themeId = $this->getActiveThemeId();
    }

    /**
     * @param $templateKey , string
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTemplateContent($templateKey)
    {
        $result = $this->client->request(
            'GET',
            sprintf('admin/themes/%s/assets.json?asset[key]=%s', $this->themeId, $templateKey),
            [
                'headers' => [
                    'X-Shopify-Access-Token' => $this->accessToken
                ]
            ]
        );

        $content = json_decode($result->getBody()->getContents(), true);

        if (!isset($content['asset']) || !isset($content['asset']['value'])) {
            throw new \Exception('Template not found');
        }

        return $content['asset']['value'];
    }

    /**
     * Get current active theme id
     * @return Response string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getActiveThemeId()
    {
        $result = $this->client->request('GET', '/admin/themes.json', [
            'headers' => [
                'X-Shopify-Access-Token' => $this->accessToken
            ]
        ]);

        $themes = json_decode($result->getBody()->getContents(), true);
        if (!count($themes['themes'])) {
            return new \Exception('Theme not found');
        }

        return $themes['themes'][0]['id'];
    }

    /**
     * @param $templateKey string
     * @return void
     * @param $content string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateTemplateContent($templateKey, $content)
    {
        $this->client->request(
            'PUT',
            sprintf('admin/themes/%s/assets.json', $this->themeId),
            [
                'headers' => [
                    'X-Shopify-Access-Token' => $this->accessToken
                ],
                'form_params' => [
                    'asset' => [
                        'key' => $templateKey,
                        'value' => $content
                    ]
                ]
            ]
        );
    }


}
