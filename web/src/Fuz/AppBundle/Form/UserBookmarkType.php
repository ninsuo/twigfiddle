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
use Fuz\AppBundle\Transformer\UserBookmarkTagTransformer;

class UserBookmarkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UserBookmarkTagTransformer($options['data_object']);

        $builder
           ->add('title', 'text', array(
                   'required' => false,
           ))
           ->add(
              $builder
              ->create('tags', 'text',
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
    public function getName()
    {
        return 'FiddleType';
    }
}
