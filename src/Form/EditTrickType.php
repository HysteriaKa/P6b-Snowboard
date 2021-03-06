<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('description', TextareaType::class, ['attr' => ['class' => 'form-control']])
            ->add('slug', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('category', EntityType::class, ['class' => Category::class, 'attr' => ['class' => 'form-control']])
            ->add('media', FileType::class, [
                'label' => 'Download pictures or videos',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('videoLink', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Have a video link you want to share ?',
                'required' => false
            ])
            ->add('OnTopPic', FileType::class, [
                'attr'=>['class'=>'bold'],
                'label' => 'The picture displayed on top (jpeg, jpg, png)',
                'label_attr'=>['class'=>'bold'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'mimeTypesMessage' => 'Please upload a valid format image',
                    ])
                ]
            ])
            ->add("Save", SubmitType::class, ['attr' => ['class' => 'btn-dark mat-2 mab-2']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
