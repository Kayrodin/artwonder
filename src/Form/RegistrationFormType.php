<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsFalse;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rol', ChoiceType::class,[
                'mapped' => false,
                'choices'  => [
                    'Usuario' => true,
                    'Agente' => false,
                ],
            ])
            ->add('nombre', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor inserte una contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} carácteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Contraseña repetida'],
                'invalid_message' => 'Contraseña repetida inválida',
                'error_bubbling' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor inserte una contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} carácteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('dni', TextType::class,
                [
                    'attr' => ['class' => 'agentForm'],
                    'label_attr'=> ['class'=> 'agentForm'],
                    'constraints' => [
                        new Regex([
                            'match' => 'false',
                            'pattern' => '/^[0-9]{8}[A-Za-z]{1}/',
                            'message' => 'Inserte un DNI válido',
                        ])
                    ]
                ])
            ->add('telefono', TelType::class,
                [
                    'attr' => ['class' => 'agentForm'],
                    'label_attr'=> ['class'=> 'agentForm'],
                ])
            ->add('cif', TextType::class,
                [
                    'attr' => ['class' => 'agentForm'],
                    'label_attr'=> ['class'=> 'agentForm'],
                    'constraints' => [
                        new Regex([
                            'match' => 'false',
                            'pattern' => '/^([ABCDEFGHJKLMNPQRSUVW])(\d{7})([0-9A-J])$/',
                            'message' => 'Inserte un CIF válido',
                        ])
                    ]
                ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Debes estar de acuerdo con nuestros términos',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
