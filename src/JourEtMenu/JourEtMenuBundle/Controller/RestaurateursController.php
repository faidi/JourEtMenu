<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JourEtMenu\JourEtMenuBundle\Entity\Medias;
use Symfony\Component\Validator\Constraints\Length;
use JourEtMenu\JourEtMenuBundle\Form\ImageUploadType;
use Proxies\__CG__\Utilisateurs\UtilisateursBundle\Entity\Restaurateurs;

class RestaurateursController extends Controller
{
    public function indexAction()
    {
    	$utilisateur = $this->container->get('security.context')->getToken()->getUser();
    	
        return $this->render('JourEtMenuBundle:Default:Restaurateurs/index.html.twig',array('utilisateur' => $utilisateur));
    }
    
    
   public function  addImagesAction(){
   	$em = $this->getDoctrine()->getManager();
   	 
   	$request = $this->get('request');
    $utilisateur = $this->container->get('security.context')->getToken()->getUser();
   
   	
   	$image=new Medias();
   	$form = $this->createForm(new ImageUploadType(),$image );
   	
   	$form->add('submit', 'submit', array('label' => 'Ajouter'));
   	
   	if ($request->getMethod() == 'POST') {
   		$form->bind($request);
   		
   		if ($form->isValid()) {
   			$image->setRestaurant($utilisateur);
   			$em->persist($image);
   			$em->flush();
   		}
   		 
   		return $this->redirect($this->generateUrl('acceuil_restaurateurs'));
   		
   	}
   	return $this->render('UtilisateursBundle:Default:compte/ajouterImages.html.twig', array(
   			'form' => $form->createView())
   	);
   	 
   }

   
   
   public function publierPlatDuJourAction(){
   	  
   	return $this->render('JourEtMenuBundle:Default:Restaurateurs/index.html.twig');
    }
   
   

   public function mesResasAction()
   {
   	$em = $this->getDoctrine()->getManager();
   	$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser();
    
    $entities = $em->getRepository('JourEtMenuBundle:Reservations')->byRestaurant($utilisateur);
   	// if($utilisateur->getType()=='Clients')
   	
   	return $this->render('JourEtMenuBundle:Default:Restaurateurs/resa/mesResas.html.twig',array('entities' => $entities));
    
   }
   
   
   
   public function confirmerResaAction($id)
   {
   	
   	$em = $this->getDoctrine()->getManager();
    
   	$status = $em->getRepository('JourEtMenuBundle:statusReservation')->find('1');
   	$entity = $em->getRepository('JourEtMenuBundle:Reservations')->find($id);
   	 
   	$entity->setStatus($status);
  	$em->persist($entity);
  	$em->flush();
   	return $this->render('JourEtMenuBundle:Default:Restaurateurs/resa/actionResas.html.twig'
   			,array('status' => $status));
   }
   
   
   public function refuserResaAction($id)
   {
   
   	$em = $this->getDoctrine()->getManager();
   
   	$status = $em->getRepository('JourEtMenuBundle:statusReservation')->find('5');
   	$entity = $em->getRepository('JourEtMenuBundle:Reservations')->find($id);
   	 
   	$entity->setStatus($status);
   	$em->persist($entity);
   	$em->flush();
   	return $this->render('JourEtMenuBundle:Default:Restaurateurs/resa/actionResas.html.twig'
   			,array('status' => $status));
   }
   
   
   
   public function annulerResaAction($id)
   {
   	 
   	$em = $this->getDoctrine()->getManager();
   	 
   	$status = $em->getRepository('JourEtMenuBundle:statusReservation')->find('2');
   	$entity = $em->getRepository('JourEtMenuBundle:Reservations')->find($id);
   	 
   	$entity->setStatus($status);
   	$em->persist($entity);
   	 
   	return $this->render('JourEtMenuBundle:Default:Restaurateurs/resa/actionResas.html.twig'
   			,array('status' => $status));
   }
   
   
   
   public function notShowResaAction($id)
   {
   	 
   	$em = $this->getDoctrine()->getManager();
   	 
   	$status = $em->getRepository('JourEtMenuBundle:statusReservation')->find('4');
   	$entity = $em->getRepository('JourEtMenuBundle:Reservations')->find($id);
   	 
   	$entity->setStatus($status);
   	$em->persist($entity);
   	 
   	return $this->render('JourEtMenuBundle:Default:Restaurateurs/resa/actionResas.html.twig'
   			,array('status' => $status));
   }
}
