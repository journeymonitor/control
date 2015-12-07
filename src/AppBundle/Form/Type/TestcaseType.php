<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
                        '*/5'  => 'Every 5 minutes',
                        '*/15' => 'Every 15 minutes',
                        '*/30' => 'Every 30 minutes',
                        '0'    => 'Every 60 minutes',
                    ],
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label'    => 'Check interval:'
                ])
            ->add('script', TextareaType::class, ['label' => 'Selenium script (HTML format only!):', 'attr' => ['class' => 'codeform']])
            ->add('Save', SubmitType::class, ['label' => 'Start monitoring', 'attr' => ['class' => 'btn-primary']]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
