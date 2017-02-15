<?php

namespace Eshop\StockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EshopStockBundle:Default:index.html.twig');
    }
}
