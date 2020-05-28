<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Regex;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class,[
                'label' => 'Nombre',
            ])
            ->add('email', EmailType::class,[
                'label' => 'Correo',
            ]);
        if ($options['rol'] == 'ROLE_AGENT'){
        $builder
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
            ->add('telefono', TelType::class,[
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
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
           'rol',
        ]);
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
