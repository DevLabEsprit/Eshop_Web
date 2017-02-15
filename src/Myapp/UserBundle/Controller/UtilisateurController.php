<?php

namespace Myapp\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

class UtilisateurController extends Controller
{
    public function facturesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('EshopStockBundle:Commandes')->byFacture($this->getUser());
        return $this->render('MyappUserBundle:default:facture.html.twig', array('factures' => $factures));
    }
    public function facturesPDFAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('EshopStockBundle:Commandes')->findOneBy(array('utilisateur' => $this->getUser(),
            'valider' => 1,
            'id' => $id));

        if (!$facture) {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
            return $this->redirect($this->generateUrl('factures'));
        }

        $html=$this->renderView('MyappUserBundle:default:facturePDF.html.twig', array('facture' => $facture));

        $html2pdf = $this->get('html2pdf_factory')->create();
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->pdf->SetAuthor('Bazzar');
        $html2pdf->pdf->SetTitle('Facture '.$facture->getReference());
        $html2pdf->pdf->SetSubject('Facture Bazzar');
        $html2pdf->pdf->SetKeywords('facture,Bazzar');
        $html2pdf->writeHTML($html);

//Output envoit le document PDF au navigateur internet
        return new Response($html2pdf->Output('facture.pdf'), 200, array('Content-Type' => 'application/pdf'));

    }
}