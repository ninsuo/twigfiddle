<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Fuz\AppBundle\Transformer\ArrayTransformer;

class BrowseFiddleType extends AbstractType
{

    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('title', 'text', array(
                   'required' => false,
           ))
           ->add(
                    $builder
                        ->create('tags', 'text', array(
                                'required' => false,
                        ))
                        ->addModelTransformer(new ArrayTransformer(','))
           )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_class' => 'Fuz\AppBundle\Entity\BrowseFiddle',
        ));
    }

    public function getName()
    {
        return 'BrowseFiddleType';
    }

}
