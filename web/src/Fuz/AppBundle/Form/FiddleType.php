<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Fuz\AppBundle\Service\ProcessConfiguration;
use Fuz\AppBundle\Transformer\FiddleTagTransformer;
use Fuz\AppBundle\Entity\Fiddle;

class FiddleType extends AbstractType
{

    protected $twigVersions;

    public function __construct(ProcessConfiguration $processConfiguration)
    {
        $cfg = $processConfiguration->getProcessConfig();

        $this->twigVersions = $cfg['supported_versions'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FiddleTagTransformer($options['data_object']);

        $builder
           ->add('twigVersion', 'choice',
              array (
                   'choices' => array_combine($this->twigVersions, $this->twigVersions),
                   'required' => true,
           ))
           ->add('templates', 'collection',
              array (
                   'type' => new FiddleTemplateType(),
                   'allow_add' => true,
                   'allow_delete' => true,
                   'prototype' => true,
                   'error_bubbling' => false,
                   'by_reference' => false,
                   'required' => false,
           ))
           ->add('context', new FiddleContextType(), array (
                   'required' => false,
           ))
           ->add('title', 'text', array (
                   'required' => false,
           ))
           ->add(
              $builder
              ->create('tags', 'text',
                 array (
                      'required' => false,
              ))
              ->addModelTransformer($transformer)
           )
           ->add('visibility', 'choice',
              array (
                   'choices' => array (
                           Fiddle::VISIBILITY_PUBLIC => 1,
                           Fiddle::VISIBILITY_UNLISTED => 2,
                           Fiddle::VISIBILITY_PRIVATE => 3,
                   )
           ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_object' => null,
                'data_class' => 'Fuz\AppBundle\Entity\Fiddle',
        ));
    }

    public function getName()
    {
        return 'FiddleType';
    }

}
