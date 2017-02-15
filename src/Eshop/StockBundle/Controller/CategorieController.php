<?php
/**
 * Created by PhpStorm.
 * User: 2017
 * Date: 31/01/2017
 * Time: 21:07
 */

namespace Eshop\StockBundle\Controller;


use Eshop\StockBundle\Entity\Categorie;
use Eshop\StockBundle\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CategorieController extends Controller
{
    public function listAction()
    {
        $em=$this->getDoctrine()->getManager();
        $categories=$em->getRepository("EshopStockBundle:Categorie")->findAll();

        return $this->render("EshopStockBundle:Categorie:list.html.twig",array(
            'categories'=>$categories
        ));

    }
    public function listfAction()
    {
        $em=$this->getDoctrine()->getManager();
        $categories=$em->getRepository("EshopStockBundle:Categorie")->findAll();

        return $this->render("EshopStockBundle:Categorie:listf.html.twig",array(
            'categories'=>$categories
        ));

    }
    public function deleteAction(Request $request , $id)
    {
        $categorie = new Categorie();

        $em = $this->getDoctrine()->getManager();
        $categorie=$em->getRepository("EshopStockBundle:Categorie")->find($id);

        $em->remove($categorie);
        $em->flush();

        return $this->redirectToRoute("eshop_stock_list");
    }
    public function ajoutAction(Request $request)
    {

        $categorie = new Categorie();
        $form=$this->createForm(CategorieType::class,$categorie);

        $form->handleRequest($request);
        if($form->isSubmitted())
        {

            $em=$this->getDoctrine()->getManager();
            $categorie->uploadProfilePicture();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute("eshop_stock_list");

        }



        return $this->render("EshopStockBundle:Categorie:ajout.html.twig",array(
            'form'=>$form->createView()
        ));

    }
    public function updateAction(Request $request , $id)
    {
        $em=$this->getDoctrine()->getManager();

        $categorie=$em->getRepository("EshopStockBundle:Categorie")->find($id);

        $form= $this->createForm(CategorieType::class,$categorie);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {



            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute("eshop_stock_list");
        }
        return $this->render("EshopStockBundle:Categorie:update.html.twig",array(
            'form'=>$form->createView()
        ));

    }
    
}