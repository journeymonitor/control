<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\TestcaseType;

class TestcaseAndUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UserType::class, ['label' => false])
            ->add('testcase', TestcaseType::class, ['label' => false]);
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
