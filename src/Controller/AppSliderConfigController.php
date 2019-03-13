<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class AppSliderConfigController extends AbstractController
{
    public function configRender()
    {
        return $this->render('app_slider_config/template.html.twig');
    }
}