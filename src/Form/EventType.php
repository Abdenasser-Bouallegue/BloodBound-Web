<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'complet' => 'complet',
                    'non complet' => 'non complet',

                ]
            ] )
            ->add('location')
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
            
            ])
            ->add('eventDate')
            ->add('startTime')
            ->add('endTime')
            ->add('user', EntityType::class,[
                "class" => User::class,
                "choice_label" => "email"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
