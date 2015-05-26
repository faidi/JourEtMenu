<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JourEtMenu\JourEtMenuBundle\Entity\statusReservation;
use JourEtMenu\JourEtMenuBundle\Form\statusReservationType;

/**
 * statusReservation controller.
 *
 * @Route("/admin/statusreservation")
 */
class statusReservationController extends Controller
{

    /**
     * Lists all statusReservation entities.
     *
     * @Route("/", name="admin_statusreservation")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JourEtMenuBundle:statusReservation')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new statusReservation entity.
     *
     * @Route("/", name="admin_statusreservation_create")
     * @Method("POST")
     * @Template("JourEtMenuBundle:statusReservation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new statusReservation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_statusreservation_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a statusReservation entity.
     *
     * @param statusReservation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(statusReservation $entity)
    {
        $form = $this->createForm(new statusReservationType(), $entity, array(
            'action' => $this->generateUrl('admin_statusreservation_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new statusReservation entity.
     *
     * @Route("/new", name="admin_statusreservation_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new statusReservation();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a statusReservation entity.
     *
     * @Route("/{id}", name="admin_statusreservation_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:statusReservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find statusReservation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing statusReservation entity.
     *
     * @Route("/{id}/edit", name="admin_statusreservation_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:statusReservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find statusReservation entity.');
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
    * Creates a form to edit a statusReservation entity.
    *
    * @param statusReservation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(statusReservation $entity)
    {
        $form = $this->createForm(new statusReservationType(), $entity, array(
            'action' => $this->generateUrl('admin_statusreservation_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing statusReservation entity.
     *
     * @Route("/{id}", name="admin_statusreservation_update")
     * @Method("PUT")
     * @Template("JourEtMenuBundle:statusReservation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:statusReservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find statusReservation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_statusreservation_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a statusReservation entity.
     *
     * @Route("/{id}", name="admin_statusreservation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JourEtMenuBundle:statusReservation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find statusReservation entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_statusreservation'));
    }

    /**
     * Creates a form to delete a statusReservation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_statusreservation_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
