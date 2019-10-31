<?php

namespace App\Form;

use App\Entity\Guest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class GuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('lastname')
            //->add('firstname')
            //->add('email')
            //->add('phone_number')
            //->add('created_at')
            //->add('updated_at')
            //->add('is_active')
            //->add('token')
            // ->add('newsletter_is_active', null, [
            //     'label' => 'Abonné à la newsletter',
            //     'required' => false
            // ])
            // ->add('is_coming', null, [
            //     'label' => 'Je serais présent au mariage',
            //     'required' => false
            // ])
            ->add('is_coming_with', IntegerType::class, [
                'label' => 'Nombre de personne au total',
                'required' => true
            ])
            ->add('vegetarian_meal_number', IntegerType::class, [
                'label' => 'Nombre de menu végétarien',
                'required' => true
            ])
            ->add('meat_meal_number', IntegerType::class, [
                'label' => 'Nombre de menu avec viande',
                'required' => true
            ])
            //->add('Type')
            //->add('projects')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Guest::class,
        ]);
    }
}
