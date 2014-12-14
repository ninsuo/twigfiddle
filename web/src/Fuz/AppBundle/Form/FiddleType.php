<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Fuz\AppBundle\Service\ProcessConfiguration;

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
        $builder
           ->add('twigVersion', 'choice', array(
                   'choices' => array_combine($this->twigVersions, $this->twigVersions),
                   'required' => true,
           ))
           ->add('templates', 'collection', array(
                   'type' => new FiddleTemplateType(),
                   'allow_add' => true,
                   'allow_delete' => true,
                   'prototype' => true,
                   'error_bubbling' => false,
                   'required' => false,
           ))
           ->add('context', new FiddleContextType(), array(
                   'required' => false,
           ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
                'data_class' => 'Fuz\AppBundle\Entity\Fiddle',
        ));
    }

    public function getName()
    {
        return 'FiddleType';
    }

}
