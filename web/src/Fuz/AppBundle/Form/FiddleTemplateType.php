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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiddleTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('filename', Type\TextType::class, array(
                   'required' => true,
           ))
           ->add('content', Type\TextareaType::class, array(
                   'required' => true,
           ))
           ->add('main', Type\CheckboxType::class, array(
                   'required' => false,
           ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Fuz\AppBundle\Entity\FiddleTemplate',
        ));
    }

    public function getBlockPrefix()
    {
        return 'FiddleTemplateType';
    }
}
