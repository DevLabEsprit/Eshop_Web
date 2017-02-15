<?php
/**
 * Created by PhpStorm.
 * User: 2017
 * Date: 04/02/2017
 * Time: 07:56
 */

namespace Eshop\StockBundle\Controller;

use Eshop\StockBundle\Entity\Produit;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ProduitController extends Controller
{
    public function listprodAction()
    {
        $em=$this->getDoctrine()->getManager();
        $produits=$em->getRepository("EshopStockBundle:Produit")->findAll();

        return $this->render("EshopStockBundle:Produit:listprod.html.twig",array(
            'produits'=>$produits
        ));



    }
    public function ajoutprodAction(Request $request)
    {

        $produit = new Produit();
        $form=$this->createForm(ProduitType::class,$produit);

        $form->handleRequest($request);
        if($form->isSubmitted())
        {

            $em=$this->getDoctrine()->getManager();
            $produit->uploadProfilePicture();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute("eshop_stock_listprod");

        }



        return $this->render("EshopStockBundle:Produit:ajoutprod.html.twig",array(
            'form'=>$form->createView()
        ));

    }
    public function listprodfAction(Request $request)
    {
        $session = $request->getSession();
        $em=$this->getDoctrine()->getManager();
        if ($session->has('panier'))
            $panier = $session->get('panier');
        else
            $panier = false;

        $produits=$em->getRepository("EshopStockBundle:Produit")->findAll();

        return $this->render("EshopStockBundle:Produit:listprodf.html.twig",array(
            'produits'=>$produits,
            'panier'=>$panier
        ));

    }
    public function presentationAction(Request $request,$id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('EshopStockBundle:Produit')->find($id);

        if (!$produit) throw $this->createNotFoundException('La page n\'existe pas.');

        if ($session->has('panier'))
            $panier = $session->get('panier');
        else
            $panier = false;

        return $this->render('EshopStockBundle:Produit:single.html.twig', array('produit' => $produit,
            'panier' => $panier));
    }

}