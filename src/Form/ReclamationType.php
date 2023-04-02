<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReclamationType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('type',ChoiceType::class, [
                'choices' => [
                    'Help' => 'Help',
                    'FeedBack' => 'Feedback',
                    'harcèlement' => 'harcèlement',

                ],
                'label' => 'Select your status',
            ])
            ->add('statut',ChoiceType::class, [
                'choices' => [
                    'Traité' => 'Traité',
                    'Non Traité' => 'non Traité',
                ],
                'label' => 'Select your status',
            ])
            ->add('idUser',EntityType::class,['class'=>User::class,'choice_label'=>'nom'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
