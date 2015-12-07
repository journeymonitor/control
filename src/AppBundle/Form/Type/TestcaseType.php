<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Testcase;

class TestcaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Testcase name:'])
            ->add(
                'cadence',
                ChoiceType::class,
                [
                    'choices' => [
                        'Every 5 minutes'  => '*/5',
                        'Every 15 minutes' => '*/15',
                        'Every 30 minutes' => '*/30',
                        'Every 60 minutes' => '0',
                    ],
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label'    => 'Check interval:'
                ])
            ->add('script', TextareaType::class, ['label' => 'Selenium script (HTML format only!):', 'attr' => ['class' => 'codeform']])
            ->add('Save', SubmitType::class, ['label' => 'Start monitoring', 'attr' => ['class' => 'btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => Testcase::class,
                'csrf_protection'    => true,
                'allow_extra_fields' => true
            ]
        );
    }

    public function getName()
    {
        return 'testcase';
    }
}
