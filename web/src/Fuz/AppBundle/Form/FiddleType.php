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
use Fuz\AppBundle\Service\TwigExtensions;
use Fuz\AppBundle\Util\ProcessConfiguration;
use Fuz\AppBundle\Transformer\FiddleTagTransformer;
use Fuz\AppBundle\Entity\Fiddle;

class FiddleType extends AbstractType
{
    protected $twigVersions;
    protected $twigExtensions;

    public function __construct(ProcessConfiguration $processConfiguration, TwigExtensions $twigExtensions)
    {
        $cfg = $processConfiguration->getProcessConfig();

        $this->twigVersions   = $cfg['supported_versions'];
        $this->twigExtensions = $twigExtensions->getAvailableTwigExtensions();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FiddleTagTransformer($options['data_object']);

        $this->buildFiddleOptions($builder, $options);

        $builder
           ->add('templates', Type\CollectionType::class, array(
               'entry_type'     => FiddleTemplateType::class,
               'allow_add'      => true,
               'allow_delete'   => true,
               'prototype'      => true,
               'error_bubbling' => false,
               'by_reference'   => false,
               'required'       => false,
           ))
           ->add('context', FiddleContextType::class, array(
               'required' => false,
           ))
           ->add('title', Type\TextType::class, array(
               'required' => false,
           ))
           ->add(
              $builder
              ->create('tags', Type\TextType::class, array(
                  'required' => false,
              ))
              ->addModelTransformer($transformer)
           )
           ->add('visibility', Type\ChoiceType::class, array(
               'choices' => array(
                   Fiddle::VISIBILITY_PUBLIC   => 1,
                   Fiddle::VISIBILITY_UNLISTED => 2,
                   Fiddle::VISIBILITY_PRIVATE  => 3,
               ),
           ))
        ;
    }

    public function buildFiddleOptions(FormBuilderInterface $builder, array $options)
    {
        $engines  = array_keys($this->twigVersions);
        $versions = array_unique(call_user_func_array('array_merge', $this->twigVersions));

        $builder
           ->add('twigEngine', Type\ChoiceType::class, array(
               'choices'  => array_combine($engines, $engines),
               'required' => true,
           ))
           ->add('twigVersion', Type\ChoiceType::class, array(
               'choices'  => array_combine($versions, $versions),
               'required' => true,
           ))
           ->add('withCExtension', Type\CheckboxType::class, array(
               'required' => false,
           ))
           ->add('twigExtension', Type\ChoiceType::class, array(
               'required' => false,
               'choices'  => array_combine($this->twigExtensions, $this->twigExtensions),
           ))
           ->add('compiledExpended', Type\CheckboxType::class, array(
               'required' => false,
           ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_object' => null,
            'data_class'  => 'Fuz\AppBundle\Entity\Fiddle',
        ));
    }

    public function getBlockPrefix()
    {
        return 'FiddleType';
    }
}
