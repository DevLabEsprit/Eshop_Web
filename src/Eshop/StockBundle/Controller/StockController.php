<?php
/**
 * Created by PhpStorm.
 * User: 2017
 * Date: 03/02/2017
 * Time: 02:33
 */

namespace Eshop\StockBundle\Controller;
use Eshop\StockBundle\Entity\Stock;
use Eshop\StockBundle\Form\StockType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StockController extends Controller
{
    public function liststokAction()
    {
        $em=$this->getDoctrine()->getManager();
        $stocks=$em->getRepository("EshopStockBundle:Stock")->findAll();

        return $this->render("EshopStockBundle:Stock:liststok.html.twig",array(
            'stocks'=>$stocks
        ));

    }
    public function deletestokAction(Request $request , $id)
    {
        $stock = new Stock();
        $em = $this->getDoctrine()->getManager();
        $stock=$em->getRepository("EshopStockBundle:Stock")->find($id);

        $em->remove($stock);
        $em->flush();

        return $this->redirectToRoute("eshop_stock_liststok");
    }

    public function ajoutstokAction(Request $request)
    {

        $stock = new Stock();
        $form=$this->createForm(StockType::class,$stock);

        $form->handleRequest($request);
        if($form->isSubmitted())
        {

            $em=$this->getDoctrine()->getManager();

            $em->persist($stock);
            $em->flush();
            return $this->redirectToRoute("eshop_stock_liststok");

        }



        return $this->render("EshopStockBundle:Stock:ajoutstok.html.twig",array(
            'form'=>$form->createView()
        ));

    }
    public function updatestokAction(Request $request , $id)
    {
        $em=$this->getDoctrine()->getManager();

        $stock=$em->getRepository("EshopStockBundle:Stock")->find($id);

        $form= $this->createForm(StockType::class,$stock);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {



            $em->persist($stock);
            $em->flush();
            return $this->redirectToRoute("eshop_stock_liststok");
        }
        return $this->render("EshopStockBundle:Stock:updatestok.html.twig",array(
            'form'=>$form->createView()
        ));

    }

}