<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TestcaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['label' => 'Testcase name'])
            ->add('notifyEmail', 'email', ['label' => 'Notification mail address'])
            ->add(
                'cadence',
                'choice',
                [
                    'choices' => [
                        '*/5' => 'Every 5 minutes',
                        '*/15' => 'Every 15 minutes',
                        '*/30' => 'Every 30 minutes',
                        '0' => 'Every 60 minutes',
                    ],
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Check interval'
                ])
            ->add('script', 'textarea', ['label' => 'Python 2 WebDriver Selenium script', 'attr' => ['class' => 'codeform']])
            ->add('Save', 'submit', ['label' => 'Start monitoring', 'attr' => ['class' => 'btn-primary']]);
    }

    public function getName()
    {
        return 'testcase';
    }
}