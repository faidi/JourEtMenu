<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JourEtMenu\JourEtMenuBundle\Entity\platDuJour;
use JourEtMenu\JourEtMenuBundle\Form\platDuJourType;
use JourEtMenu\JourEtMenuBundle\Form\MenusType;
use JourEtMenu\JourEtMenuBundle\Entity\Menus;
use JourEtMenu\JourEtMenuBundle\Entity\Entrees;
use JourEtMenu\JourEtMenuBundle\Form\EntreesType;
use JourEtMenu\JourEtMenuBundle\Form\digestifType;
use JourEtMenu\JourEtMenuBundle\Entity\Aperitif;
use JourEtMenu\JourEtMenuBundle\Entity\Plats;
use JourEtMenu\JourEtMenuBundle\Form\PlatsType;
use JourEtMenu\JourEtMenuBundle\Entity\Desserts;
use JourEtMenu\JourEtMenuBundle\Form\DessertsType;
use JourEtMenu\JourEtMenuBundle\Entity\digestif;
use JourEtMenu\JourEtMenuBundle\Form\AperitifType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * platDuJour controller.
 *
 * @Route("/restaurant/menusc")
 */
class MenusController extends Controller {
	 
	
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/", name="Menus")
	 *  
	 *  
	 */
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser();
	
		// $entities = $em->getRepository('JourEtMenuBundle:platDuJour')->findAll();
		$entities = $em->getRepository('JourEtMenuBundle:Menus')->byRestaurant($utilisateur);
		return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/mesMenus.html.twig' , array(
				'entities' => $entities
		));
	}
	
	
	/**
	 * Création du menu et ces elements.
	 *
	 * @Route("/board", name="BoardMenuscreation")
	 */
	public function commencerMenuAction(){
	
	
		return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/quoifaire.html.twig'  );
	
	}
	/**
	 * Création du menu et ces elements.
	 *
	 * @Route("/creer", name="Menuscreation")
	 */
	public function creerMenusAction() {
		
		$session = $this->get('session');
		
		$em = $this->getDoctrine ()->getManager ();
		$menu = new Menus ();
		$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser ();
		
		$form = $this->createForm ( new MenusType (), $menu );
		$request = $this->get ( 'request' );
		$form->add ( 'submit', 'submit', array (
				'label' => 'Creer' 
		) );
		
		if ($request->getMethod () == 'POST') {
			$form->bind ( $request );
			
			if ($form->isValid ()) {
				$menu->setRestaurant ( $utilisateur );
				$em->persist ( $menu );
				
				$em->flush ();
				
				
				if($session->has('aperos')){
					$aperitifs=$session->get('aperos');
					foreach ($aperitifs as $aperitif)  {
						$aperitif->setMenu($menu);
						$em->persist($aperitif);
					}
				}
				if($session->has('Entrees')){
					
					$entrees=$session->get('Entrees');
					foreach ($entrees as $entree)  {
						$entree->setMenu($menu);
						$em->persist($entree);
					}
				}
				if($session->has('plats')){
					$plats=$session->get('plats');
					foreach ($plats as $plat)  {
						$plat->setMenu($menu);
						$em->persist($plat);
					}
				}
				if($session->has('desserts')){
					$desserts=$session->get('desserts');
					foreach ($desserts as $dessert)  {
						$dessert->setMenu($menu);
						$em->persist($dessert);
					}
						
				}
				if($session->has('digestifs')){
					$digestifs=$session->get('digestifs');
					foreach ($digestifs as $digestif)  {
						$digestif->setMenu($menu);
						$em->persist($digestif);
					}
				}
				
				 $em->flush();
				 
				 $this->get('session')->remove('aperos');
				 $this->get('session')->remove('Entrees');
				 $this->get('session')->remove('plats');
				 $this->get('session')->remove('desserts');
				 $this->get('session')->remove('digestifs');
				 	
				 return $this->render ( 'JourEtMenuBundle:Default:Restaurateurs/creationMenus/succesCreationMenu.html.twig' 
				  );
			}
		}
		return $this->render ( 'JourEtMenuBundle:Default:Restaurateurs/creerMenu.html.twig', array (
				'form' => $form->createView () 
		) );
	}
	
	/**
	 * Création du menu et ces elements.
	 *
	 * @Route("/creerAp", name="APcreation")
	 */
	public function creeAperitifAction() {
		
		$session = $this->get('session');
		
		
 		$entity = new Aperitif ();
 		$form = $this->createForm ( new AperitifType(), $entity );
		$request = $this->get ( 'request' );
 		$form->add ( 'submit', 'submit', array (
				'label' => 'Creer'));
	 		if ($request->getMethod() == 'POST') {
			$form->bind($request);
	
			if ($form->isValid()) {
			 
			 if($session->has('aperos')){
			 	
			 	$aperitifs=$session->get('aperos');
			 	array_push($aperitifs, $entity);
			 	$session->set('aperos', $aperitifs);
			 }else{
			 	$aperitifs = array ();
			 	$aperitifs[]=$entity  ;
			 	$session->set('aperos', $aperitifs);
			 }
			 
			 $session->set('aperos', $aperitifs);
				return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/quoifaire.html.twig'  );
			}
	
	
		}
	
		return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/creerAperitif.html.twig',array(
				'form' => $form->createView()));
	}
	
	
	
	/**
	 * Création du menu et ces elements.
	 *
	 * @Route("/creerEn", name="Encreation")
	 */
	public function creeEntreeAction(){
		$session = $this->get('session');
		 
		$entity=new Entrees();
		$form = $this->createForm(new EntreesType(), $entity );
		$request = $this->get('request');
		$form->add('submit', 'submit', array('label' => 'Creer'));
		
		
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
		
			if ($form->isValid()) {
				if($session->has('Entrees')){
			 	
			 	$aperitifs=$session->get('Entrees');
			 	array_push($aperitifs, $entity);
			 	$session->set('Entrees', $aperitifs);
			 }else{
			 	$aperitifs = array ();
			 	$aperitifs[]=$entity  ;
			 	$session->set('Entrees', $aperitifs);
			 }
			 
			 $session->set('Entrees', $aperitifs);
				 return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/quoifaire.html.twig');
			}
		
		
		}
		
		return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/creerEntrees.html.twig',array(
				'form' => $form->createView()));
	}
	
	
	
	
	/**
	 * Création du menu et ces elements.
	 *
	 * @Route("/creerPl", name="Plcreation")
	 */
	public function creePlatAction(){
		$session = $this->get('session');
			
		$entity=new Plats();
			
	
		$form = $this->createForm(new PlatsType(), $entity );
		$request = $this->get('request');
	
	
	
		$form->add('submit', 'submit', array('label' => 'Creer'));
	
	
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
	
			if ($form->isValid()) {
				if($session->has('plats')){
			 	
			 	$aperitifs=$session->get('plats');
			 	array_push($aperitifs, $entity);
			 	$session->set('plats', $aperitifs);
			 }else{
			 	$aperitifs = array ();
			 	$aperitifs[]=$entity  ;
			 	$session->set('plats', $aperitifs);
			 }
			 
			 $session->set('plats', $aperitifs);
				return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/quoifaire.html.twig');
			}
	
	
		}
	
		return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/creerPlat.html.twig',array(
				'form' => $form->createView()));
	
}




/**
 * Création du menu et ces elements.
 *
 * @Route("/creerDes", name="Descreation")
 */
public function creeDessertAction(){
	$session = $this->get('session');
		
	$entity=new Desserts();
		

	$form = $this->createForm(new DessertsType(), $entity );
	$request = $this->get('request');



	$form->add('submit', 'submit', array('label' => 'Creer'));


	if ($request->getMethod() == 'POST') {
		$form->bind($request);

		if ($form->isValid()) {
			if($session->has('desserts')){
			 	
			 	$aperitifs=$session->get('desserts');
			 	array_push($aperitifs, $entity);
			 	$session->set('desserts', $aperitifs);
			 }else{
			 	$aperitifs = array ();
			 	$aperitifs[]=$entity  ;
			 	$session->set('desserts', $aperitifs);
			 }
			 
			 $session->set('desserts', $aperitifs);
			return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/quoifaire.html.twig');
		}


	}

	return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/creerDesserts.html.twig',array(
			'form' => $form->createView()));
}




/**
 * Création du menu et ces elements.
 *
 * @Route("/creerDig", name="Digcreation")
 */
public function creeDigestifAction(){
	$session = $this->get('session');
		
	$entity=new digestif();
		

	$form = $this->createForm(new digestifType(), $entity );
	$request = $this->get('request');



	$form->add('submit', 'submit', array('label' => 'Creer'));


	if ($request->getMethod() == 'POST') {
		$form->bind($request);

		if ($form->isValid()) {
			if($session->has('digestifs')){
			 	
			 	$aperitifs=$session->get('digestifs');
			 	array_push($aperitifs, $entity);
			 	$session->set('digestifs', $aperitifs);
			 }else{
			 	$aperitifs = array ();
			 	$aperitifs[]=$entity  ;
			 	$session->set('digestifs', $aperitifs);
			 }
			 
			 $session->set('digestifs', $aperitifs);
			return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/quoifaire.html.twig');
		}


	}

	return $this->render('JourEtMenuBundle:Default:Restaurateurs/creationMenus/creerDigestifs.html.twig',array(
			'form' => $form->createView()));

}


}