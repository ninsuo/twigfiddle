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

class FiddleTemplateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('filename', 'text', array(
                   'required' => true,
           ))
           ->add('content', 'textarea', array(
                   'required' => true,
           ))
           ->add('main', 'checkbox', array(
                   'required' => false,
           ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
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
