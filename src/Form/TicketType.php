<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price')
            ->add('PricePt')
            ->add('stock')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'available' => 'available',
                    'sold out' => 'sold out',

                ]
            ])
            ->add('event', EntityType::class,[
                "class" => Event::class,
                "choice_label" => "title"
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
