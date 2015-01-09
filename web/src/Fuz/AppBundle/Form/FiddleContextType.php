<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Fuz\AppBundle\Entity\FiddleContext;

class FiddleContextType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('format', 'choice', array(
                   'choices' => array_combine(FiddleContext::getSupportedFormats(), FiddleContext::getSupportedFormats()),
                   'required' => true,
           ))
           ->add('content', 'textarea', array(
                   'required' => false,
           ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_object' => null,
                'data_class' => 'Fuz\AppBundle\Entity\FiddleContext',
        ));
    }

    public function getName()
    {
        return 'FiddleContextType';
    }

}
