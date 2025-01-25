<?php

    namespace App\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class ImportStatFromSpreadSheetFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('file', FileType::class, [
                    'label' => 'Importer un fichier',
                    'multiple' => false,
                    'mapped' => false,
                    'required' => false,
                    'data_class' => null,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                                       // Configure your form options here
                                   ]);
        }
    }
