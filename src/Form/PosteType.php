<?php

namespace App\Form;

use App\Entity\Poste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformerInterface;
class PosteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder
            ->add('titre')
            ->add('description')
            ->add('img', FileType::class, [
                'label' => 'Image (JPG, PNG or GIF file)',
                
            ])
            ->add('domaine', ChoiceType::class, [
                'choices' => [
                    'INFO' => 'INFO',
                    'ELECTROMECANIQUE' => 'ELECTROMECANIQUE',
                    'SANTE' => 'SANTE',
                ],
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Choose a domaine', // optional
            ])
            
            ->get('img')
            ->addModelTransformer(new class implements DataTransformerInterface {
                public function transform($value)
                {
                    return null;
                }

                public function reverseTransform($value)
                {
                    if (!$value) {
                        return;
                    }

                    if (is_string($value)) {
                        return new File($this->uploadsDirectory.'/'.$value);
                    }

                    return $value;
                }
                });
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poste::class,
        ]);
    }
}
