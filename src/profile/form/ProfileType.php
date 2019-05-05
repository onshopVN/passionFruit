<?php

namespace App\profile\form;

use App\profile\entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => false, 'constraints' => [ new NotBlank(['groups'=>['create','update']])] ])
            ->add('gender', TextType::class, ['required' => false, 'constraints' => [ new NotBlank(['groups'=>['create','update']])] ])
            ->add('contact', TextType::class, ['required' => false,'constraints' => [ new NotBlank(['groups'=>['update']])] ])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('company', TextareaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => new GroupSequence(['list','create', 'update']),
            'data_class' => Profile::class,
        ]);
    }
}