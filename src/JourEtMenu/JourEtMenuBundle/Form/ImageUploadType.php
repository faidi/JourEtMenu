<?php

namespace JourEtMenu\JourEtMenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
class ImageUploadType extends AbstractType{
	
	
	
    public function buildForm(FormBuilderInterface $builder, array
    $options) {
       
        $builder->add('file','file' )
           ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'JourEtMenu\JourEtMenuBundle\Entity\Medias'));
    }
	public function getName()
	{
		return 'imageuploadtype';
	}
	
}