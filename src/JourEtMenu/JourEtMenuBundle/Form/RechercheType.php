<?php

namespace JourEtMenu\JourEtMenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RechercheType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('quoi', 'text', array('label' => false,
                    'attr' => array('class' => 'input', 'id' => 'quoi')))
                ->add('ou', 'text', array(
                    'label' => 'Field'
        ));
    }

    public function getName() {
        return 'bar_recherche';
    }

}
