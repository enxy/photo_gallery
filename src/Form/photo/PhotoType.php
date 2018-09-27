<?php
/**
 * Photo type.
*/
namespace Form\photo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class PhotoType.
 *
 * @package Form
 */
class PhotoType extends AbstractType
{
    /**
     * Form for adding photos.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'description',
            TextType::class,
            [
                'label' => 'label.description',
                'required' => true,
                'attr' => [
                    'max_length' => 158,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
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
            'path',
            FileType::class,
            [
                'label' => 'label.path',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Image(
                        [
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/pjpeg',
                                'image/jpeg',
                                'image/pjpeg',
                            ],
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'is_public',
            ChoiceType::class,
            [
                'label' => 'label.is_public',
                'choices' => [
                    'label.no' => 0,
                    'label.yes' => 1,
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['bookmark-default']]
                    ),
                    new Assert\Choice(
                        [
                            'groups' => ['bookmark-default'],
                            'choices' => [0, 1],
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'label.tags',
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        );
        $builder->get('tags')->addModelTransformer(
            new TagsDataTransformer($options['tag_repository'])
        );
    }

    /**
     * Configure options.
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'bookmark-default',
                'tag_repository' => null,
            ]
        );
    }

    /**
     * Get block prefix.
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'photo_type';
    }

    /**
     * Prepare tags
     * @param $tagRepository
     * @return array
     */
    function prepareTagsForChoices($tagRepository)
    {
        $tags = $tagRepository->findAll();
        $choices = [];

        foreach ($tags as $tag) {
            $choices[$tag['name']] = $tag['tagId'];
        }
        return $choices;
    }
}