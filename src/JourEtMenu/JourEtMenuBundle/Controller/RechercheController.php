<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use JourEtMenu\JourEtMenuBundle\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Utilisateurs\UtilisateursBundle\Entity\Restaurateurs;

/**
 * platDuJour controller.
 *
 * @Route("/")
 */
class RechercheController extends Controller {
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/recherche/resultat", name="recherche")
	 */
	public function RechercheResAction() {
		$form = $this->createForm ( new RechercheType () );
		
		//$form->bind ( $this->get ( 'request' ) );
		$em = $this->getDoctrine()->getManager();
		
		// $motCle=$form['quoi']->getData();
		
		$arr1 = $em->getRepository( 'JourEtMenuBundle:Menus' )->findAll();
		$arr2 = $em->getRepository( 'JourEtMenuBundle:platDuJour' )->findAll();
		/*
		 * $arr2= $this->getDoctrine()
		 * ->getEntityManager()
		 * ->getRepository('InterventionBundle:Professionnels')
		 * ->findByMotCleSp($motCle); 
		 */
		
		// $resultats = $arr1 + $arr2;
		return $this->render( 'JourEtMenuBundle:Client:Resultat_Recherche.html.twig', array('resultats' => $arr1,'plats' => $arr2));
    
    } 

    /**
     * Lists all platDuJour entities.
     *
     * @Route("/recherche/bar", name="rechercheBar")
     *
     *
     */
    public function rechercheAction(){
        $form = $this->createForm(new RechercheType );
                    return $this->render('JourEtMenuBundle:Default:recherche/Recherche.html.twig', array('form' => $form->createView()));

    }
   
}
  