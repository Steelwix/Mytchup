<?php

namespace App\Form;

use App\Entity\Champion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PushNewStatFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstChampion', EntityType::class, [
                'class' => Champion::class,
                'choice_label' => 'name',
                'placeholder' => '- Select -',
                'required' => true,
                'attr' => [
                    'id' => 'firstChampion'
                ]
            ])
            ->add('secondChampion', EntityType::class, [
                'class' => Champion::class,
                'choice_label' => 'name',
                'placeholder' => '- Select -',
                'required' => true,
            ])
            ->add('game_won', ChoiceType::class, [
                'choices' => [
                    '- Select -' => null,
                    'Yes' => true,
                    'No' => false,
                ],
                'label' => 'Did you win the game?',

            ])
            ->add('matchup_won', ChoiceType::class, [
                'choices' => [
                    '- Select -' => null,
                    'Yes' => true,
                    'Even' => null,
                    'No' => false,
                ],
                'label' => 'Did you win your matchup in lane?',


            ])
            ->add('save', SubmitType::class, [
                'label' => 'Submit',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
