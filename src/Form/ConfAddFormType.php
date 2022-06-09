<?php

namespace App\Form;

use App\Entity\Conference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfAddFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Titre de la conférence',
                'required' => true
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année',
                'required' => true
            ])
            ->add('isInternational', CheckboxType::class, [
                'label' => 'Internationnal ?',
                'required' => false
            ])
            ->add('photoFileName', FileType::class, [
                'label' => 'Photo (optionnel)',
                'required' => false,
                'mapped' => false
            ])
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conference::class,
        ]);
    }
}
