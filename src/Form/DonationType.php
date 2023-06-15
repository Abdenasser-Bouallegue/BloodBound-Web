<?php

namespace App\Form;

use App\Entity\Donation;
use App\Entity\Emergency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('donLocation')
            ->add('email')
            ->add('phoneNumber')
            ->add('donationDate')
            /*->add('emergency', EntityType::class, [
                'class'=>Emergency::class,
                'choice_label'=>'title'
            ])*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
        ]);
    }
}
