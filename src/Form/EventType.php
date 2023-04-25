<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\EventCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class)
            ->add('date', DateTimeType::class)
            ->add('eventCategory', EntityType::class, [
                'class' => EventCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a category',
            ])
            ->add('location')
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'Event',
            ])
            ->add('img', FileType::class, [
                'label' => 'Image (JPG, PNG or GIF file)',

            ])
            ->get('img')
            ->addModelTransformer(new class implements DataTransformerInterface
            {
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
                        return new File($this->uploadsDirectory . '/' . $value);
                    }

                    return $value;
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
