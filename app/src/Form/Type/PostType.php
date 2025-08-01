<?php

/**
 * Post type.
 */

namespace App\Form\Type;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Symfony\Component\Validator\Constraints\All;

/**
 * Class PostType.
 */
class PostType extends AbstractType
{
    /**
     * TagsDataTransformer instance.
     *
     * @param TagsDataTransformer $tagsDataTransformer The tags data transformer
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
                    'label' => 'form.label.post_title',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'form.placeholder.post_title',
                        'max_length' => 128,
                    ],
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'placeholder' => 'form.placeholder.post_category',
                    'label' => 'form.label.post_category',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'form.placeholder.post_category',
                        'max_length' => 128,
                    ],
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'form.label.post_content',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'form.placeholder.post_content',
                        'max_length' => 2048,
                        'style' => 'height: 740px',
                    ],
                ]
            )
            ->add(
                'tags',
                TextType::class,
                [
                    'label' => 'form.label.tags',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'form.placeholder.tags',
                        'max_length' => 64,
                    ],
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' => [
                        'form.status.draft' => 'draft',
                        'form.status.published' => 'published',
                    ],
                    'label' => 'form.label.post_status',
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add(
                'images',
                FileType::class,
                [
                    'mapped' => false,
                    'label' => 'form.label.post_images',
                    'required' => false,
                    'multiple' => true,
                    'constraints' => [
                        new All([
                            'constraints' => [
                                new ImageConstraint([
                                    'maxSize' => '2M',
                                    'mimeTypes' => [
                                        'image/png',
                                        'image/jpeg',
                                        'image/pjpeg',
                                        'image/webp',
                                    ],
                                ]),
                            ],
                        ]),
                    ],
                ]
            );

        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
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
            'data_class' => Post::class,
        ]);
    }

    /**
     * Get block prefix.
     *
     * @return string the block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'post';
    }
}
