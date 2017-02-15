<?php
namespace Eshop\StockBundle\Services;

use Symfony\Component\Security\Core\SecurityContextInterface;

class GetReference 
{
    public function __construct($entityManager)
    {

        $this->em = $entityManager;
    }
    
    public function reference()
    {
        $reference = $this->em->getRepository('EshopStockBundle:Commandes')->findOneBy(array('valider' => 1), array('id' => 'DESC'),1,1);
        
        if (!$reference)
            return 1;
        else 
            return $reference->getReference() +1;
    }
}