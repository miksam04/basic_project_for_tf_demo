<?php

/**
 * Tag type form.
 */

namespace App\Form\Type;

use App\Entity\Tag;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagType.
 *
 * This class defines the form type for tag entities.
 */
class TagType extends AbstractType
{
    /**
     * TagType constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer The data transformer for tags
     */
    public function __construct(private readonly TagsDataTransformer $tagsDataTransformer)
    {
    }

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
                'title',
                TextType::class,
                [
                    'label' => 'form.label.tag_title',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'form.placeholder.tag_title',
                        'max_length' => 64,
                    ],
                ]
            );
    }

    /**
     * Configure options for the form.
     *
     * @param OptionsResolver $resolver Options resolver
     *
     * @return void returns nothing
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }

    /**
     * Get block prefix.
     *
     * @return string The block prefix for the form type
     */
    public function getBlockPrefix(): string
    {
        return 'tag';
    }
}
