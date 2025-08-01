<?php

/**
 * User type.
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * Class UserType.
 *
 * This class defines the form type for user entities.
 */
class UserType extends AbstractType
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
                'nickname',
                TextType::class,
                [
                    'label' => 'form.label.nickname',
                    'attr' => [
                        'required' => true,
                        'placeholder' => 'form.placeholder.nickname',
                        'max_length' => 20,
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'form.label.email',
                    'attr' => [
                        'required' => true,
                        'placeholder' => 'form.placeholder.email',
                        'max_length' => 180,
                    ],
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'label' => 'form.label.password',
                    'mapped' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'form.placeholder.optional_password',
                        'max_length' => 255,
                    ],
                ]
            );
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
