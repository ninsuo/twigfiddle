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

use Fuz\AppBundle\Transformer\UserBookmarkTagTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserBookmarkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UserBookmarkTagTransformer($options['data_object']);

        $builder
           ->add('title', Type\TextType::class, array(
                   'required' => false,
           ))
           ->add(
              $builder
              ->create('tags', Type\TextType::class,
                 array(
                      'required' => false,
              ))
              ->addModelTransformer($transformer)
           )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_object' => null,
                'data_class' => 'Fuz\AppBundle\Entity\UserBookmark',
        ));
    }

    /**
     * We use the same type name to handle the same form whenever
     * user saves a fiddle or bookmark customizations.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'FiddleType';
    }
}
