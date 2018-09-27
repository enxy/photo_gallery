<?php
/**
 * rating type.
 */
namespace Form\photo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class RatingType.
 *
 * @package Form
 */
class RatingType extends AbstractType
{
    /**
     * Form for photo rating.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'number',
            ChoiceType::class,
            [
                'label' => 'label.grade',
                'choices' => [
                    'label.bad' => 1,
                    'label.poor' => 2,
                    'label.average' => 3,
                    'label.good' => 4,
                    'label.excelent' => 5,
                ],

            ]
        );
    }
}