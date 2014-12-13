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
                   'choices' => array_intersect($this->twigVersions, $this->twigVersions),
                   'required' => true,
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
