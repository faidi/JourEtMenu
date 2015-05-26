<?php

namespace JourEtMenu\JourEtMenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RechercheType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('quoi', 'text', array('label' => false,
                    'attr' => array('class' => 'input', 'id' => 'quoi')))
                
                    ->add('quand','datetime', array('data' => new \DateTime('now')))
                    
                   ->add('specialite', 'choice', array(
            'choices'   => array(
                '5'   => '5/5',
                '4' => '4/5',
                '3 ' => '3/5',
                '2 ' => '2/5','3 ' => '1/5','0 ' => '0/5',

                ),
            'multiple'  => false,
            'label' => false,
            'empty_value' => 'Spécialité'
            ))
                
                ->add('quand', 'date', array(
                    'label' => 'Field'
        
                		
                		
                		
                ));
    }

    public function getName() {
        return 'bar_recherche';
    }

}
