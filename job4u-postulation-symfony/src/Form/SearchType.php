<?php

namespace App\Form;

use App\Entity\Postulation;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\PostulationRepository;
use App\Repository\UsersRepository;





class SearchType extends AbstractType
{
    private $PostulationRepository;
    private $UsersRepository;

   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creator')
            ->add('Nom')
            ->add('email', TextType::class, [
                'label' => 'Title',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Postulation::class,
            'data_class' => Users::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}