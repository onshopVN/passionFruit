<?php

namespace App\authen\form;

use App\authen\entity\Authen;
use App\profile\entity\Profile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['required' => false, 'constraints' => [new NotBlank(['groups' => ['create', 'update', 'login']])]])
            ->add('username', TextType::class, ['required' => false, 'constraints' => [new NotBlank(['groups' => ['create', 'update']])]])
            ->add('fullname', TextType::class, ['required' => false, 'constraints' => [new NotBlank(['groups' => ['create', 'update']])]])
            ->add('password', PasswordType::class, ['required' => false, 'constraints' => [new NotBlank(['groups' => ['update', 'login']])]])
            ->add('profile', EntityType::class, [
                'class' => Profile::class,
                'choice_label' => 'name',
                'required' => false,
                'constraints' => [new NotBlank(['groups' => ['create', 'update']])]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => new GroupSequence(['list', 'create', 'update', 'login']),
            'data_class' => Authen::class,
        ]);
    }
}