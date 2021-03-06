<?php

namespace JourEtMenu\JourEtMenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReservationsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date','datetime', array('data' => new \DateTime('now')))
            ->add('nbrPersonne')
            ->add('message', 'text' ,array( 'required' => false))
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JourEtMenu\JourEtMenuBundle\Entity\Reservations'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jouretmenu_jouretmenubundle_reservations';
    }
}
