<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-07-21
 * Time: 21:55
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DatePickerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('widget' => 'single_text'
            ,'format' => 'dd/MM/yyyy'
            ,'attr' => array('class'=>'datepicker'
                ,'data-dateformat'=>'dd/mm/yy'
                ,'placeholder'=>'Select a date'
                )
            )
        );
    }

    public function getParent()
    {
        return DateType::class;
    }
}