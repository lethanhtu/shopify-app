<?php

namespace App\Library\Shopify;

use GuzzleHttp\Client;

/**
 * Class ShopifyRequest
 * @package App\Service\Shopify
 */
class ShopifyRequest
{
    protected $header;
    protected $client;
    protected $themeId;


    public function __construct()
    {
        $this->header = [
            'X-Shopify-Access-Token' => ShopifyAuth::getAccessToken()
        ];
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
            sprintf('/admin/themes/%s/assets.json?asset[key]=%s', $this->themeId, $templateKey),
            [
                'headers' => $this->header
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
            'headers' => $this->header
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
            sprintf('/admin/themes/%s/assets.json', $this->themeId),
            [
                'headers' => $this->header,
                'form_params' => [
                    'asset' => [
                        'key' => $templateKey,
                        'value' => $content
                    ]
                ]
            ]
        );
    }

    /**
     * @param $scriptLink string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addScriptTag($scriptLink)
    {
        $result = $this->client->request(
            'POST',
            '/admin/script_tags.json',
            [
                'headers' => $this->header,
                'form_params' => [
                    'script_tag' => [
                        'event' => 'onload',
                        'src' => $scriptLink
                    ]
                ]
            ]

        );

        file_put_contents('debug.txt', $result->getBody()->getContents(), 8);


    }


    public function deleteScriptTag($scriptTagId)
    {
        $this->client->request(
            'DELETE',
            sprintf('/admin/script_tags/%s.json', $scriptTagId),
            [
                'headers' => $this->header
            ]

        );
    }

    public function registerWebhook()
    {
        $this->client->request(
            'POST',
            '/admin/webhooks.json',
            [
                'headers' => $this->header,
                'form_params' => [
                    'webhook' => [
                        'topic' => 'app/uninstalled',
                        'address' => sprintf('%s/slider/uninstall',getenv('APP_URL')),
                        'format' => 'json'
                    ]
                ]
            ]
        );
    }
}

