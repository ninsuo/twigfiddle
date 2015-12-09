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

use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Transformer\ArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrowseFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add(
              $builder
              ->create('keywords', Type\TextType::class, array(
                  'required' => false,
              ))
              ->addModelTransformer(new ArrayTransformer(' '))
           )
           ->add(
              $builder
              ->create('tags', Type\TextType::class, array(
                  'required' => false,
              ))
              ->addModelTransformer(new ArrayTransformer(','))
           )
           ->add('bookmark', Type\CheckboxType::class, array(
               'required' => false,
           ))
           ->add('mine', Type\CheckboxType::class, array(
               'required' => false,
           ))
           ->add('visibility', Type\ChoiceType::class, array(
               'choices' => array(
                   ucfirst(Fiddle::VISIBILITY_PUBLIC) => Fiddle::VISIBILITY_PUBLIC,
                   ucfirst(Fiddle::VISIBILITY_UNLISTED) => Fiddle::VISIBILITY_UNLISTED,
                   ucfirst(Fiddle::VISIBILITY_PRIVATE) => Fiddle::VISIBILITY_PRIVATE,
               ),
               'choices_as_values' => true,
               'required' => false,
           ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fuz\AppBundle\Entity\BrowseFilters',
        ));
    }

    public function getBlockPrefix()
    {
        return 'BrowseFiltersType';
    }
}
