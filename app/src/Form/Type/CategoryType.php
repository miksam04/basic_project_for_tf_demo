<?php

/**
 * Category type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType.
 */
class CategoryType extends AbstractType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Options Form options
     *
     * @return void returns nothing
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'form.label.category_name',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'form.placeholder.category_name',
                        'max_length' => 64,
                    ],
                ]
            );
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Options resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    /**
     * Get block prefix.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'category';
    }
}
