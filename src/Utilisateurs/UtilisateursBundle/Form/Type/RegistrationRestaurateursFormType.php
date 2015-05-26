<?php

namespace Utilisateurs\UtilisateursBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationRestaurateursFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->add('telephone')
        ->add('nom')
        ->add('specialite', 'entity', array( 'class' => 'JourEtMenu\JourEtMenuBundle\Entity\Specialite', 'property' => 'nom',
'multiple' => FALSE));
       
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'restaurateurs_registration';
    }
}