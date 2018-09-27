<?php
/**
 * edit tag type.
 */
namespace Form\photo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EditTagType
 * @package Form
 */
class EditTagType extends AbstractType
{
    /**
     * Build form to edit tags.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Add new tag:',
                'attr' => [
                    'max_length' => 158,
                ],
                'constraints' => [
                    new Assert\Length(
                        [
                            'min' => 3,
                            'max' => 158,
                        ]
                    ),
                ],
            ]
        );
    }
}