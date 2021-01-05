<?php

namespace App\Form;

use App\Entity\Milestone;
use App\Entity\Status;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nazwa',
            ])
            ->add('employee', null, [
                'label' => 'Pracownik',
            ])
            ->add('deadline', null, [
                'label' => 'Termin realizacji',
                'widget' => 'single_text',
                'attr' => ['class' => 'picker']
            ])
            ->add('description', null, [
                'label' => 'Opis zadania',
            ])
            ->add('status', EntityType::class, [
                'label' => 'Status zadania',
                'class' => Status::class,
            ])
            ->add('milestone', EntityType::class, [
                'label' => 'Kamień milowy',
                'class' => Milestone::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
