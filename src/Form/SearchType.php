<?php

namespace App\Form;

use App\Entity\Postulation;
use App\Entity\User;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\PostulationRepository;
use App\Repository\UsersRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('creator', ChoiceType::class, [
            'label' => 'Creator',
            'choices' => $options['creators'],
            'choice_label' => 'name',
            'placeholder' => 'Select a creator',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('creators');
        $resolver->setAllowedTypes('creators', 'array');
    }
}
