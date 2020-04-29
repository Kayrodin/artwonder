<?php

namespace App\Form;

use App\Entity\MarcaAutor;
use App\Entity\WondArt;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class WondArtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', TextType::class, [
                'required' => true,
            ])
            ->add('media', FileType::class, [
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid jpeg/jpg/png/webp images',
                        'uploadPartialErrorMessage' => 'No se completo la subida del archivo',
                        'maxSizeMessage' => 'El archivo es mayor que {{ size }}'
                    ]),
                ],
            ])
            ->add('historia', TextareaType::class)
            ->add('etiquetas')
            ->add('marcaAutor', EntityType::class,[
                'class' => MarcaAutor::class,
                'required' => true,
                'choice_label' => 'nombre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WondArt::class,
        ]);
    }
}
