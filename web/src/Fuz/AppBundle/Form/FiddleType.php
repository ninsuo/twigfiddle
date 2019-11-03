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
use Fuz\AppBundle\Service\TwigExtensions;
use Fuz\AppBundle\Util\ProcessConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiddleType extends AbstractType
{
    protected $twigVersions;
    protected $twigExtensions;
    protected $environment;

    public function __construct(ProcessConfiguration $processConfiguration, TwigExtensions $twigExtensions, $environment)
    {
        $cfg = $processConfiguration->getProcessConfig();

        $this->twigVersions   = $cfg['supported_versions'];
        $this->twigExtensions = $twigExtensions->getAvailableTwigExtensions();
        $this->environment    = $environment;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildFiddleOptions($builder, $options);

        $builder
           ->add('templates', Type\CollectionType::class, [
               'entry_type'     => FiddleTemplateType::class,
               'allow_add'      => true,
               'allow_delete'   => true,
               'prototype'      => true,
               'error_bubbling' => false,
               'by_reference'   => false,
               'required'       => false,
           ])
           ->add('context', FiddleContextType::class, [
               'required' => false,
           ])
           ->add('title', Type\TextType::class, [
               'required' => false,
           ])
           ->add('visibility', Type\ChoiceType::class, [
               'choices' => [
                   1 => Fiddle::VISIBILITY_PUBLIC,
                   2 => Fiddle::VISIBILITY_UNLISTED,
                   3 => Fiddle::VISIBILITY_PRIVATE,
               ],
               'choices_as_values' => true,
           ])
        ;
    }

    public function buildFiddleOptions(FormBuilderInterface $builder, array $options)
    {
        $engines  = array_keys($this->twigVersions);
        $versions = array_unique(call_user_func_array('array_merge', $this->twigVersions));

        $builder
           ->add('twigEngine', Type\ChoiceType::class, [
               'choices'           => array_combine($engines, $engines),
               'required'          => true,
               'choices_as_values' => true,
           ])
           ->add('twigVersion', Type\ChoiceType::class, [
               'choices'           => array_combine($versions, $versions),
               'required'          => true,
               'choices_as_values' => true,
           ])
           ->add('withStrictVariables', Type\CheckboxType::class, [
               'required' => false,
           ])
           ->add('twigExtension', Type\ChoiceType::class, [
               'required'          => false,
               'choices'           => array_combine($this->twigExtensions, $this->twigExtensions),
               'choices_as_values' => true,
           ])
           ->add('compiledExpended', Type\CheckboxType::class, [
               'required' => false,
           ])
        ;

        if ('dev' === $this->environment) {
            $builder
               ->add('debug', Type\CheckboxType::class, [
                   'required' => false,
               ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_object' => null,
            'data_class'  => 'Fuz\AppBundle\Entity\Fiddle',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'FiddleType';
    }
}
