<?php

namespace App\Form;

use App\Entity\Emergency;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EmergencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('bloodType',ChoiceType::class, [
                'choices'  => [
                    '' => '',
                    'A+' => 'A+',
                    'B+' => 'B+',
                    'AB+' => 'AB+',
                    'O+' => 'O+',
                    'A-' => 'A-',
                    'B-' => 'B-',
                    'AB-' => 'AB-',
                    'O-' => 'O-',

            ],
    ])

            ->add('location')
            ->add('deadline')
            ->add('status'
                ,ChoiceType::class, [
                    'choices'  => [
                        'In progress' => 'in progress',
                        'Completed' => 'completed',
                    ],
                ])
            ->add('createdAt')
            ->add('user', EntityType::class,[
                "class" => User::class,
                "choice_label" => "email"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emergency::class,
        ]);
    }
}
