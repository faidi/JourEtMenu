<?php
namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JourEtMenu\JourEtMenuBundle\Entity\Reservations;
use JourEtMenu\JourEtMenuBundle\Entity\Menus;
use JourEtMenu\JourEtMenuBundle\Form\ReservationsType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JourEtMenu\JourEtMenuBundle\Entity\platDuJour;
/**
 * platDuJour controller.
 *
 * @Route("/reservation")
 * 
 */
class ReservationsController extends Controller
{
	
	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/{menu}", name="reserver")
	 *
	 *
	 */
	public function reservermenuAction(Menus $menu )
	{
		$em = $this->getDoctrine ()->getManager ();
		$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser();
		$entities = $em->getRepository('JourEtMenuBundle:statusReservation')->find('3');
		$entity=new Reservations();
		$entity->setClient($utilisateur);
		$form = $this->createForm ( new ReservationsType(), $entity );
		$request = $this->get ( 'request' );
		$form->add ( 'submit', 'submit', array (
				'label' => 'Je Reserve',
		'attr'=> array('class'=>'btn btn-success')) );
		
		if ($request->getMethod () == 'POST') {
			$form->bind ( $request );
				
			if ($form->isValid ()) {
				  
				$entity->setStatus($entities);
				$entity->setMenu($menu);
				$entity->setRestaurant($menu->getRestaurant());
				$em->persist($entity);
				$em->flush();
				return $this->render ( 'JourEtMenuBundle:Client:resa/succesreservation.html.twig'
				);
				
			}
			 
		
		}
		 
		return $this->render('JourEtMenuBundle:Client:resa/index.html.twig', array('form' => $form->createView (),'menu' =>$menu ));
	} 

	/**
	 * Lists all platDuJour entities.
	 *
	 * @Route("/plat/{platDuJour}", name="reserver_platdujour")
	 *
	 *
	 */
	public function reserverPlatDuJourAction(platDuJour $platDuJour )
	{
		$em = $this->getDoctrine ()->getManager ();
		$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser();
		$entities = $em->getRepository('JourEtMenuBundle:statusReservation')->find('3');
		
		
		$entity=new Reservations();
		
		
		
		$entity->setClient($utilisateur);
		$form = $this->createForm ( new ReservationsType(), $entity );
		$request = $this->get ( 'request' );
		$form->add ( 'submit', 'submit', array (
				'label' => 'Reserver'
		) );
	
		if ($request->getMethod () == 'POST') {
			$form->bind ( $request );
	
			if ($form->isValid ()) {
	
				$entity->setStatus($entities);
				$entity->setPlatDuJour($platDuJour);
				$entity->setRestaurant($platDuJour->getRestaurant());
				$em->persist($entity);
				$em->flush();
				return $this->render ( 'JourEtMenuBundle:Client:resa/succesreservation.html.twig'
				);
	
			}
	
	
		}
			
		return $this->render('JourEtMenuBundle:Client:resa/index.html.twig', array('form' => $form->createView (),'menu' =>$platDuJour ));
	}
	
}
