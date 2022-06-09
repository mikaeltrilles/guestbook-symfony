<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre nom',
                ],
            ])
            ->add('text', TextType::class, [
                'label' => 'Commentaire',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre commentaire',
                ],
            ])
            // J'ajoute l'email
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre email',
                ],
            ])
            ->add('createdAt')
            ->add('note', IntegerType::class, [
                'label' => 'Note',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre note',
                ],
            ])
            ->add('conference')
            // J'ajoute le bouton submit
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'href' => '',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
