<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Fuz\AppBundle\Transformer\ArrayTransformer;
use Fuz\AppBundle\Entity\Fiddle;

class BrowseFiltersType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add(
                    $builder
                        ->create('keywords', 'text', array(
                                'required' => false,
                        ))
                        ->addModelTransformer(new ArrayTransformer(' '))
           )
           ->add(
                    $builder
                        ->create('tags', 'text', array(
                                'required' => false,
                        ))
                        ->addModelTransformer(new ArrayTransformer(','))
           )
           ->add('bookmark', 'checkbox', array(
                   'required' => false,
           ))
           ->add('mine', 'checkbox', array(
                   'required' => false,
           ))
           ->add('visibility', 'choice', array(
                   'choices' => array(
                        Fiddle::VISIBILITY_PUBLIC => ucfirst(Fiddle::VISIBILITY_PUBLIC),
                        Fiddle::VISIBILITY_UNLISTED => ucfirst(Fiddle::VISIBILITY_UNLISTED),
                        Fiddle::VISIBILITY_PRIVATE => ucfirst(Fiddle::VISIBILITY_PRIVATE),
                   )
           ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_class' => 'Fuz\AppBundle\Entity\BrowseFilters',
        ));
    }

    public function getName()
    {
        return 'BrowseFiltersType';
    }

}
