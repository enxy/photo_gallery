<?php
/**
 * Created by PhpStorm.
 * User: Jolanta
 * Date: 11.07.2017
 * Time: 21:52
 */
namespace Form\user;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserType
 * @package Form
 */
class UserType extends AbstractType{
    /**
     * Build form for users.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'login',
            TextType::class,
            [
                'label' => 'label.login',
                'attr' => [
                    'max_length' => 128,
                ]
            ])->add(
            'name',
            TextType::class,
            [
                'label' => 'user.name',
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        )->add(
            'surname',
            TextType::class,
            [
                'label' => 'user.surname',
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        )->add(
            'email',
            EmailType::class,
            [
                'label'=>'user.email',
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        )->add(
        'role_id',
        ChoiceType::class,
        [
            'label'=>'user.role',
            'choices'=>[
                'label.admin'=>1,
                'label.user'=>2
            ]
        ]
    );
    }
}