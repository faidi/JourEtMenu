<?php

namespace JourEtMenu\JourEtMenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JourEtMenu\JourEtMenuBundle\Entity\Medias;
use JourEtMenu\JourEtMenuBundle\Form\ImageUploadType;
class platDuJourType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('nom','text')
            ->add('prix')
            ->add('desciption','text')
            ->add('disponible')
            ->add('ingredients')
            ->add('updateAt')
        ;
    }
    
     
    
     
 
    /**
     * @return string
     */
    public function getName()
    {
        return 'jouretmenu_jouretmenubundle_platdujour';
    }
}
