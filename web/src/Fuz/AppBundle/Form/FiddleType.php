<?php

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Fuz\AppBundle\Service\ProcessConfiguration;

class FiddleType extends AbstractType
{

    protected $processConfiguration;

    public function __construct(ProcessConfiguration $processConfiguration)
    {
        $this->processConfiguration = $processConfiguration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {




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
