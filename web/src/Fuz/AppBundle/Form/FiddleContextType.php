<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Fuz\AppBundle\Entity\FiddleContext;

class FiddleContextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('format', Type\ChoiceType::class, array(
                   'choices' => array_combine(FiddleContext::getSupportedFormats(), FiddleContext::getSupportedFormats()),
                   'required' => true,
                   'choices_as_values' => true,
           ))
           ->add('content', Type\TextareaType::class, array(
                   'required' => false,
           ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_object' => null,
                'data_class' => 'Fuz\AppBundle\Entity\FiddleContext',
        ));
    }

    public function getBlockPrefix()
    {
        return 'FiddleContextType';
    }
}
