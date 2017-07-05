<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUi\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitTypeIconExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('icon', $options['icon']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['icon'] = $options['icon'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['icon' => null]);
        $resolver->setDefined(['icon']);
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return SubmitType::class;
    }
}
