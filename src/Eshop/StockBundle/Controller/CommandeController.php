<?php

namespace Eshop\StockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Eshop\StockBundle\Entity\Commandes;
use Symfony\Component\HttpFoundation\Response;
class CommandeController extends Controller
{
   public function facture(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$generator = $this->container->get('security.secure_random');
        $session = $request->getSession();
        $adresse = $session->get('adresse');
        $panier = $session->get('panier');
        $commande = array();
        $totalHT = 0;
        $totalTTC = 0;

        $facturation = $em->getRepository('EshopStockBundle:UtilisateursAdresses')->find($adresse['facturation']);
        $livraison = $em->getRepository('EshopStockBundle:UtilisateursAdresses')->find($adresse['livraison']);
        $produits = $em->getRepository('EshopStockBundle:Produit')->findArray(array_keys($session->get('panier')));


        foreach($produits as $produit)
        {
            $prixHT = $produit->getPrix();
            $prixTTC = ($produit->getPrix() * $panier[$produit->getId()]);
            $totalHT += $prixHT;
            $totalTTC += $prixTTC;


            $commande['produit'][$produit->getId()] = array('reference' => $produit->getNom(),
                'quantite' => $panier[$produit->getId()],
                'prixHT' => round($produit->getPrix(),2),
                'prixTTC' => round($produit->getPrix())
            );
        }

        $commande['livraison'] = array('prenom' => $livraison->getPrenom(),
            'nom' => $livraison->getNom(),
            'telephone' => $livraison->getTelephone(),
            'adresse' => $livraison->getAdresse(),
            'cp' => $livraison->getCp(),
            'ville' => $livraison->getVille(),
            'pays' => $livraison->getPays(),
            'complement' => $livraison->getComplement());

        $commande['facturation'] = array('prenom' => $facturation->getPrenom(),
            'nom' => $facturation->getNom(),
            'telephone' => $facturation->getTelephone(),
            'adresse' => $facturation->getAdresse(),
            'cp' => $facturation->getCp(),
            'ville' => $facturation->getVille(),
            'pays' => $facturation->getPays(),
            'complement' => $facturation->getComplement());

        $commande['prixHT'] = round($totalHT,2);
        $commande['prixTTC'] = round($totalTTC,2);
        //$commande['token'] = bin2hex($generator->nextBytes(20));

        return $commande;

    }

    public function prepareCommandeAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if (!$session->has('commande'))
            $commande = new Commandes();
        else
            $commande = $em->getRepository('EshopStockBundle:Commandes')->find($session->get('commande'));

        $commande->setDate(new \DateTime());
        $commande->setUtilisateur($this->container->get('security.token_storage')->getToken()->getUser());
        $commande->setValider(0);
        $commande->setReference(0);
        $commande->setCommande($this->facture($request));

        if (!$session->has('commande')) {
            $em->persist($commande);
            $session->set('commande',$commande);
        }

        $em->flush();

        return new Response($commande->getId());
    }

    public function validationCommandeAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('EshopStockBundle:Commandes')->find($id);

        if (!$commande || $commande->getValider() == 1)
            throw $this->createNotFoundException('La commande n\'existe pas');

        $commande->setValider(1);
        $commande->setReference($this->container->get('setNewReference')->Reference()); //Service
        $em->flush();

        $session = $request->getSession();
        $session->remove('adresse');
        $session->remove('panier');
        $session->remove('commande');

        $message = \Swift_Message::newInstance()
            ->setSubject('Validation de votre commande')
            ->setFrom(array('bazzartunisie@gmail.com' => "Bazzar"))
            ->setTo($commande->getUtilisateur()->getEmailCanonical())
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody($this->renderView('EshopStockBundle:MailView:Contenumail.html.twig',array('utilisateur' => $commande->getUtilisateur(),
                'commande'=>$commande                    )));
            $this->get('mailer')->send($message);


        $this->get('session')->getFlashBag()->add('success','Votre commande est validé avec succès');
        return $this->redirect($this->generateUrl('factures'));
    }

    public function annulationCommandeAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('EshopStockBundle:Commandes')->find($id);


        $commande->setValider(0);
        $commande->setReference($this->container->get('setNewReference')->Reference()); //Service
        $em->flush();

        $session = $request->getSession();
        $session->remove('adresse');
        $session->remove('panier');
        $session->remove('commande');

        $message = \Swift_Message::newInstance()
            ->setSubject('Annulation de votre commande')
            ->setFrom(array('bazzartunisie@gmail.com' => "Bazzar"))
            ->setTo($commande->getUtilisateur()->getEmailCanonical())
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody($this->renderView('EshopStockBundle:MailView:Contenumail2.html.twig',array('utilisateur' => $commande->getUtilisateur(),
                                                                                                    'commande' =>$commande              )));
        $this->get('mailer')->send($message);


        $this->get('session')->getFlashBag()->add('success','Votre commande est Annuler avec succès');
        return $this->redirect($this->generateUrl('factures'));
    }
}