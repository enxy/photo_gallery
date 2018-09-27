<?php
/**
 * Created by PhpStorm.
 * User: Jolanta
 * Date: 26.05.2017
 * Time: 20:40
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GalleryType.
 *
 * @package Form
 */
class GalleryType extends AbstractType
{
    /**
     * Build form to add name and surnmae.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['required' => true, 'attr' => ['max_length' => 128,],])
        ->add('surname', TextType::class, ['required'=>true, 'attr' => [ 'max_length' => 128,]])

        ;
    }

    /**
     * Get block prefix method.
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'tag_type';
    }
}