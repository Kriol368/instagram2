<?php

namespace App\Form;

use App\Entity\Post;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class PostFormType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Post Content',
                'attr' => [
                    'placeholder' => 'Write your post here...',
                    'rows' => 5,
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Content cannot be blank']),
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'Content cannot exceed {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('publishedAt', DateTimeType::class, [
                'data' => new DateTime(),
                'widget' => 'single_text',
                'label' => false,
                'html5' => true,
                'attr' => [
                    'style' => 'display:none;',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Attach Image or Video (Optional)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '10M', // Increase size limit to accommodate video files
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'video/mp4',
                            'video/quicktime', // Common video MIME types
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF) or video (MP4, MOV) file',
                    ]),
                ],
            ])
            ->add('numLikes', HiddenType::class, [
                'data' => 0,
            ])
            ->add('postUser', HiddenType::class, [
                'data' => $this->security->getUser(),
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}