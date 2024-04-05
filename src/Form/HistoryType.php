<?php

namespace App\Form;

use App\Entity\ChatSession;
use App\Entity\ChatVersion;
use App\Entity\History;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('prompt')
            ->add('ts', null, [
                'widget' => 'single_text',
            ])
            ->add('version', EntityType::class, [
                'class' => ChatVersion::class,
                'choice_label' => 'id',
            ])
            ->add('session', EntityType::class, [
                'class' => ChatSession::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => History::class,
        ]);
    }
}
