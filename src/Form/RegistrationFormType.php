<?php

namespace App\Form;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'w-full border border-gray-300 py-2 px-3 rounded focus:outline-none focus:border-blue-500',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'w-full border border-gray-300 py-2 px-3 rounded focus:outline-none focus:border-blue-500',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('firstname', null, [
                'label' => 'First Name',
                'attr' => [
                    'class' => 'w-full border border-gray-300 py-2 px-3 rounded focus:outline-none focus:border-blue-500',
                ],
            ])
            ->add('lastname', null, [
                'label' => 'Last Name',
                'attr' => [
                    'class' => 'w-full border border-gray-300 py-2 px-3 rounded focus:outline-none focus:border-blue-500',
                ],
            ])
            ->add('birthdate', null, [
                'label' => 'Date de naissance',
                'years' => range(1900, (new DateTimeImmutable())->format('Y')),
                'attr' => [
                    'class' => 'w-full border border-gray-300 py-2 px-3 rounded focus:outline-none focus:border-blue-500',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Agree to terms',
                'attr' => [
                    'class' => 'form-checkbox text-blue-600 rounded focus:outline-none focus:border-blue-500',
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class' => 'bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-500 mt-4',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
