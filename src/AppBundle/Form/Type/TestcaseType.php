<?php


namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestcaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['label' => 'Testcase name'])
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
            ->add('script', 'textarea', ['label' => 'Selenium script (HTML format only!)', 'attr' => ['class' => 'codeform']]);

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'      => 'AppBundle\Entity\Testcase',
                'csrf_protection' => true,
                'allow_extra_fields' => true
            ]
        );
    }

    public function getName()
    {
        return 'testcase';
    }
}