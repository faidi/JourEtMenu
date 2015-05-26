<?php

namespace JourEtMenu\JourEtMenuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JourEtMenu\JourEtMenuBundle\Entity\Specialite;
use JourEtMenu\JourEtMenuBundle\Form\SpecialiteType;

/**
 * Specialite controller.
 *
 * @Route("/admi/specialite")
 */
class SpecialiteController extends Controller
{

    /**
     * Lists all Specialite entities.
     *
     * @Route("/", name="admin_specialite")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JourEtMenuBundle:Specialite')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Specialite entity.
     *
     * @Route("/", name="admin_specialite_create")
     * @Method("POST")
     * @Template("JourEtMenuBundle:Specialite:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Specialite();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_specialite_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Specialite entity.
     *
     * @param Specialite $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Specialite $entity)
    {
        $form = $this->createForm(new SpecialiteType(), $entity, array(
            'action' => $this->generateUrl('admin_specialite_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Specialite entity.
     *
     * @Route("/new", name="admin_specialite_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Specialite();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Specialite entity.
     *
     * @Route("/{id}", name="admin_specialite_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:Specialite')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialite entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Specialite entity.
     *
     * @Route("/{id}/edit", name="admin_specialite_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:Specialite')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialite entity.');
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
    * Creates a form to edit a Specialite entity.
    *
    * @param Specialite $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Specialite $entity)
    {
        $form = $this->createForm(new SpecialiteType(), $entity, array(
            'action' => $this->generateUrl('admin_specialite_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Specialite entity.
     *
     * @Route("/{id}", name="admin_specialite_update")
     * @Method("PUT")
     * @Template("JourEtMenuBundle:Specialite:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JourEtMenuBundle:Specialite')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialite entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_specialite_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Specialite entity.
     *
     * @Route("/{id}", name="admin_specialite_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JourEtMenuBundle:Specialite')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Specialite entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_specialite'));
    }

    /**
     * Creates a form to delete a Specialite entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_specialite_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
