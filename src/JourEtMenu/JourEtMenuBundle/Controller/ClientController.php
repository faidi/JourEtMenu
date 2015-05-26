<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Utilisateurs\UtilisateursBundle\Entity\Restaurateurs;


/**
 * platDuJour controller.
 *
 * @Route("/")
 */
class ClientController extends Controller
{
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/", name="acceuil")
	 *
	 *
	 */
    public function indexAction( )
    {
    	
    	$em = $this->getDoctrine ()->getManager ();
    	 $entities = $em->getRepository('JourEtMenuBundle:Menus')->findAll( );
    	
        return $this->render('JourEtMenuBundle:Client:index.html.twig', array('menus' => $entities));
    }

    /**
     * Lists all platDuJour entities.
     *
     * @Route("/mesResasC", name="mesResasC")
     *
     *
     */
    public function mesResasAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser();
    
    	$entities = $em->getRepository('JourEtMenuBundle:Reservations')->byClient($utilisateur);
    	// if($utilisateur->getType()=='Clients')
    
    	return $this->render('JourEtMenuBundle:Client:resa/mesResas.html.twig',array('entities' => $entities));
    
    }
    
    /**
     * Lists all platDuJour entities.
     *
     * @Route("/{id}", name="showRestaurant")
     *
     *
     */
    public function showRestaurantAction($id )
    {
    	 
    	$em = $this->getDoctrine ()->getManager ();
    	$entity = $em->getRepository('UtilisateursBundle:Restaurateurs')->find($id );
    	 
    	return $this->render('JourEtMenuBundle:Client:restaurant.html.twig', array('entity' => $entity));
    }
    
    
    
    
    /**
     * Lists all platDuJour entities.
     *
     * @Route("/reservation/{id}/annuler", name="annuler")
     *
     *
     */
    
    public function annulerResaAction($id)
    {
    	 
    	$em = $this->getDoctrine()->getManager();
    	 
    	$status = $em->getRepository('JourEtMenuBundle:statusReservation')->find('2');
    	$entity = $em->getRepository('JourEtMenuBundle:Reservations')->find($id);
    	 
    	$entity->setStatus($status);
    	$em->persist($entity);
    	$em->flush();
    	$this->get('session')->getFlashBag()->add(
    			'notice',
    			'Votre réservation a été annuler avec succés !'
    	);
    	return $this->forward('JourEtMenuBundle:Client:mesResas');
    	//return $this->render('JourEtMenuBundle:Client:restaurant.html.twig', array('entity' => $entity));
    	
    }
     
}
