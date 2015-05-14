<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestcaseAndUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', new UserType())
            ->add('testcase', new TestcaseType())
            ->add('Save', 'submit', ['label' => 'Start monitoring', 'attr' => ['class' => 'btn-primary']]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolverInterface)
    {
        $resolverInterface->setDefaults([
           'label' => false
        ]);
    }

    public function getName()
    {
        return 'testcase_and_user';
    }
}