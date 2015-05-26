<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JourEtMenu\JourEtMenuBundle\Entity\platDuJour;
use JourEtMenu\JourEtMenuBundle\Form\platDuJourType;
use JourEtMenu\JourEtMenuBundle\Entity\Medias;
use JourEtMenu\JourEtMenuBundle\Form\ImageUploadType;

/**
 * platDuJour controller.
 *
 * @Route("/restaurant/platsdujour")
 */
class platDuJourController extends Controller
{

    /**
     * Lists all platDuJour entities.
     *
     * @Route("/", name="restaurant_platsdujour")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser();
        
       // $entities = $em->getRepository('JourEtMenuBundle:platDuJour')->findAll();
        $entities = $em->getRepository('JourEtMenuBundle:platDuJour')->byRestaurant($utilisateur);

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new platDuJour entity.
     *
     * @Route("/", name="restaurant_platsdujour_create")
     * @Method("POST")
     * @Template("JourEtMenuBundle:platDuJour:new.html.twig")
     */
    public function createAction(Request $request)
    {
    	$utilisateur = $this->container->get ( 'security.context' )->getToken ()->getUser();
    	 
        $entity = new platDuJour();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setRestaurant($utilisateur);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('restaurant_platsdujour_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a platDuJour entity.
     *
     * @param platDuJour $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(platDuJour $entity)
    {
        $form = $this->createForm(new platDuJourType(), $entity, array(
            'action' => $this->generateUrl('restaurant_platsdujour_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new platDuJour entity.
     *
     * @Route("/new", name="restaurant_platsdujour_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new platDuJour();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
     * Displays a form to create a new platDuJour entity.
     *
     * @Route("/newImg/{id}", name="restaurant_platsdujour_addImg")
     *  
     *  
     */
    public function newImgAction($id)
    {
    	//$entity = new platDuJour();
    	$em = $this->getDoctrine()->getManager();
     	$request = $this->get('request');
     	$image=new Medias();
     	$form = $this->createForm(new ImageUploadType(),$image );
     	$entity = $em->getRepository('JourEtMenuBundle:platDuJour')->find($id);
     	$form->add('submit', 'submit', array('label' => 'Ajouter'));
     	
     	
     	if ($request->getMethod() == 'POST') {
     		$form->bind($request);
     		 
     		if ($form->isValid()) {
     			$image->setPlatdujour($entity);
     			$em->persist($image);
     			$em->flush();
     		}
     		 
     return $this->redirect($this->generateUrl('restaurant_platsdujour'));

    }
    return $this->render('JourEtMenuBundle:platDuJour:ajouterImages.html.twig', array(
    		'form' => $form->createView(),'entity' => $entity)
    );
    
    }
    
    
    /**
     * Finds and displays a platDuJour entity.
     *
     * @Route("/{id}", name="restaurant_platsdujour_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:platDuJour')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find platDuJour entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    
    
    

    /**
     * Displays a form to edit an existing platDuJour entity.
     *
     * @Route("/{id}/edit", name="restaurant_platsdujour_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:platDuJour')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find platDuJour entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a platDuJour entity.
    *
    * @param platDuJour $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(platDuJour $entity)
    {
        $form = $this->createForm(new platDuJourType(), $entity, array(
            'action' => $this->generateUrl('restaurant_platsdujour_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing platDuJour entity.
     *
     * @Route("/{id}", name="restaurant_platsdujour_update")
     * @Method("PUT")
     * @Template("JourEtMenuBundle:platDuJour:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:platDuJour')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find platDuJour entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('restaurant_platsdujour_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a platDuJour entity.
     *
     * @Route("/{id}", name="restaurant_platsdujour_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JourEtMenuBundle:platDuJour')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find platDuJour entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('restaurant_platsdujour'));
    }

    /**
     * Creates a form to delete a platDuJour entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('restaurant_platsdujour_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
