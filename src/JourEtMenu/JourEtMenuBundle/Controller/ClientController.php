<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Utilisateurs\UtilisateursBundle\Entity\Restaurateurs;
use Reactorcoder\Symfony2NodesocketBundle\Library\php\NodeSocket as NodeSocket;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Utilisateurs\UtilisateursBundle\UtilisateursBundle;
use JourEtMenu\JourEtMenuBundle\Entity\Favoris;

/**
 * platDuJour controller.
 *
 * @Route("/")
 */
class ClientController extends Controller {
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/", name="acceuil")
	 */
	public function indexAction() {
		/*
		 * $event = $this->get('service_nodesocket')->getFrameFactory()->createEventFrame();
		 * $event->setEventName('message');
		 * $event['url'] = "uri";
		 * $event['time'] = date("d.m.Y H:i");
		 * $event['message'] = 'Hello';
		 * $event->send();
		 */
		// socket_create($domain, $type, $protocol);
		// $faye = $this->container->get ( 'NomDuBundle.faye.client' );
		
		// construction d'un message
		
		// $channel = '/messages';
		// $data = array (
		// 'text' => 'Lorem ipsum dolor sin amet...'
		// );
		
		// envoi du message
		
		// $faye->send ( $channel, $data );
		$em = $this->getDoctrine ()->getManager ();
		$entities = $em->getRepository ( 'JourEtMenuBundle:Menus' )->findAll ();
		
		return $this->render ( 'JourEtMenuBundle:Client:index.html.twig', array (
				'menus' => $entities 
		) );
	}
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/mesResasC", name="mesResasC")
	 */
	public function mesResasAction() {
		$em = $this->getDoctrine ()->getManager ();
		$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser ();
		
		$entities = $em->getRepository ( 'JourEtMenuBundle:Reservations' )->byClient ( $utilisateur );
		// if($utilisateur->getType()=='Clients')
		
		return $this->render ( 'JourEtMenuBundle:Client:resa/mesResas.html.twig', array (
				'entities' => $entities 
		) );
	}
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/{id}", name="showRestaurant")
	 */
	public function showRestaurantAction($id) {
		$em = $this->getDoctrine ()->getManager ();
		$entity = $em->getRepository ( 'UtilisateursBundle:Restaurateurs' )->find ( $id );
		
		return $this->render ( 'JourEtMenuBundle:Client:restaurant.html.twig', array (
				'entity' => $entity 
		) );
	}
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/reservation/{id}/annuler", name="annuler")
	 */
	public function annulerResaAction($id) {
		$em = $this->getDoctrine ()->getManager ();
		
		$status = $em->getRepository ( 'JourEtMenuBundle:statusReservation' )->find ( '2' );
		$entity = $em->getRepository ( 'JourEtMenuBundle:Reservations' )->find ( $id );
		
		$entity->setStatus ( $status );
		$em->persist ( $entity );
		$em->flush ();
		$this->get ( 'session' )->getFlashBag ()->add ( 'notice', 'Votre réservation a été annuler avec succés !' );
		return $this->forward ( 'JourEtMenuBundle:Client:mesResas' );
		// return $this->render('JourEtMenuBundle:Client:restaurant.html.twig', array('entity' => $entity));
	}
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/{id}/Ajouter_favoris/", name="ajouter_favoris", options={"expose"=true})
	 */
	public function addFavorissAction($id) {
		$request = $this->getRequest ();
		
		
		if ($request->isXmlHttpRequest ()) {
			$em = $this->getDoctrine ()->getManager ();
			$favoris = $em->getRepository ( 'UtilisateursBundle:Restaurateurs' )->find ( $id );
			$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser ();
			$favoris = $em->getRepository ( 'UtilisateursBundle:Restaurateurs' )->find ( $id );
				
			

			if ($utilisateur->getRestaurantfavoris()->contains($favoris)) {
				$output = array (
						'success' => false
				);
				
			} else {
				$utilisateur->addRestaurantfavori ( $favoris );
				$favoris->addClient ( $utilisateur );
				$em->persist ( $favoris );
				$em->persist ( $utilisateur );
				$em->flush ();
				$output = array (
						'success' => true
				);
			}
				
			$return = json_encode ( $output ); // jscon encode the array
			return new Response ( $return, 200, array (
					'Content-Type' => 'application/json' 
			) );
		}
	}

	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/mesFavoris/restaurants", name="mesFavorisR")
	 */
public function mesFavorisAction(){
	
	
	return $this->render ( 'JourEtMenuBundle:Client:favoris.html.twig');
	
}
}