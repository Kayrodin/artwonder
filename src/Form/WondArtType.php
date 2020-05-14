<?php

namespace App\Form;

use App\Entity\MarcaAutor;
use App\Entity\WondArt;
use App\Repository\MarcaAutorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;

class WondArtType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var MarcaAutorRepository
     */
    private $autorRepository;


    /**
     * WondArtType constructor.
     * @param Security $security
     * @param MarcaAutorRepository $autorRepository
     */
    public function __construct(Security $security, MarcaAutorRepository $autorRepository)
    {
        $this->security = $security;
        $this->autorRepository = $autorRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('titulo', TextType::class, [
                'required' => true,
            ])
            ->add('media', FileType::class, [
                'required' => true,
                "data_class" => null,   //no restringe el tipo de dato que le llega en el ajax
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
            ->add('historia', TextareaType::class, [
                'required'=>false,
            ])

            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
                $userId = $this->security->getUser()->getId();
                $form = $event->getForm();
                $formOptions = [
                    'class' => MarcaAutor::class,
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder'=> function(MarcaAutorRepository $autorRepository) use ($userId){
                        return $autorRepository->findMySigns($userId);
                    }
                ];
                $results = $this->autorRepository->findAllByMy($userId);

                $form->add('marcaAutor', EntityType::class, $formOptions);

            })
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WondArt::class,
            'media' => null,
        ]);
    }
}
