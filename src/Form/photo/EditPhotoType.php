<?php
/**
 * photo type.
 */
namespace Form\photo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class EditPhotoType
 * @package Form
 */
class EditPhotoType extends AbstractType
{
    /**
     * Build form to edit photos.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
        'description',
            TextType::class,
            [
                'label' => 'New description:',
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
        $builder->add(
            'is_public',
            ChoiceType::class,
            [
                'label'=>'Public:',
                'choices' => [
                    'label.no' => 0,
                    'label.yes' => 1,
                ],
                'required' => true
            ]
        );


    }
}