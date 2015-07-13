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
		 
		
		 
		$em = $this->getDoctrine ()->getManager ();
		
		 
		$arr1 = $em->getRepository ( 'JourEtMenuBundle:Menus' )->findAll ();
		$arr2 = $em->getRepository ( 'JourEtMenuBundle:platDuJour' )->findAll ();
		 
		return $this->render ( 'JourEtMenuBundle:Client:Resultat_Recherche.html.twig', array (
				'resultats' => $arr1,
				'plats' => $arr2 
		) );
	}
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/recherche/bar", name="rechercheBar")
	 */
	public function rechercheAction() {
		
		// endroit
		// date:
		// /type:tous, Menus , platJour
		// distance:
		
		// promotion: soit choisit
		// distance:inférieur à
		// prix:inférieur à
		$form = $this->createForm ( new RechercheType () );
		return $this->render ( 'JourEtMenuBundle:Default:recherche/Recherche.html.twig', array (
				'form' => $form->createView () 
		) );
	}
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/recherche/menu/entrees/{id}", name="findmenu", options={"expose"=true})
	 */
	public function findMenuAction(Request $request, $id) {
		$request = $this->get ( 'request' );
		
		if ($request->isXmlHttpRequest ()) {
		
			$menu = $this->getDoctrine ()->getEntityManager ()->getRepository ( 'JourEtMenuBundle:Menus' )->find ($id);
			$entrees = $menu->getEntrees ();
			
			if ($entrees) {
				
				$entreeNoms = array ();
				foreach ( $entrees as $entree ) {
					$entreeNoms [] = $entree->getNom ();
				}
			} else {
				$entreeNoms = null;
			}
			
			$response = new JsonResponse ();
			return $response->setData ( array (
					'entree' => $entreeNoms 
			) );
		} else 

		{
			throw new \Exception ( 'Erreur' );
		}
		
		$response = new Response ( json_encode ( $entrees ) );
		
		$response->headers->set ( 'Content-Type', 'application/json' );
		return $response;
	}
}
   

  