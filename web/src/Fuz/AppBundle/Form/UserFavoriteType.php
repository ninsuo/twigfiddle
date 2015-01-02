<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Fuz\AppBundle\Transformer\UserFavoriteTagTransformer;

class UserFavoriteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UserFavoriteTagTransformer($options['data_object']);

        $builder
           ->add('title', 'text', array(
                   'required' => false,
           ))
           ->add(
                    $builder
                        ->create('tags', 'text', array(
                                'required' => false,
                        ))
                        ->addModelTransformer($transformer)
           )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_object' => null,
                'data_class' => 'Fuz\AppBundle\Entity\UserFavorite',
        ));
    }

    /**
     * We use the same type name to handle the same form whenever
     * user saves a fiddle or favorite customizations.
     *
     * @return string
     */
    public function getName()
    {
        return 'FiddleType';
    }

}
