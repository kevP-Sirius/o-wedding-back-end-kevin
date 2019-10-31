<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('name')
            //->add('deadline')
            //->add('forecast_budget')
            //->add('current_budget')
            //->add('created_at')
            //->add('updated_at')
            //->add('is_active')
            ///->add('token')
            //->add('user')
            //->add('department')
            //->add('provider')
            //->add('guest')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
