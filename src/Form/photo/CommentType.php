<?php
/**
 * comment type.
 */
namespace Form\photo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CommentType
 * @package Form
 */
class CommentType extends AbstractType
{
    /**
     * Comments form.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options ){
        $builder->add(
            'comment',
            TextareaType::class,
            [
                'label' => 'label.comment',
                'required'=>true,
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