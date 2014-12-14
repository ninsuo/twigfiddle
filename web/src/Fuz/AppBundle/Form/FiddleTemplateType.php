<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FiddleTemplateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('filename', 'text', array(
                   'required' => false,
           ))
           ->add('content', 'textarea', array(
                   'required' => false,
           ))
           ->add('isMain', 'checkbox', array(
                   'required' => false,
           ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_class' => 'Fuz\AppBundle\Entity\FiddleTemplate',
        ));
    }

    public function getName()
    {
        return 'FiddleTemplateType';
    }

}
