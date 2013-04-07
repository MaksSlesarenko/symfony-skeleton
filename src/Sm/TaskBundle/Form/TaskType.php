<?php

namespace Sm\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null, array(
                'required' => true
            ))
            ->add('due_at', 'datetime', array(
                'required' => false,
                'years' => range(date('Y'), date('Y') + 5),
                'minutes' => array('00', '15', '30', '45'),
                'date_format' => 'dd&nbsp;MMM&nbsp;yyyy',
            ))
            ->add('priority', 'entity', array(
                'required' => true,
                'property' => 'name',
                'class' => 'SmTaskBundle:Priority'
            ));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sm\TaskBundle\Model\TaskModel'
        ));
    }

    public function getName()
    {
        return 'sm_taskbundle_tasktype';
    }
}
