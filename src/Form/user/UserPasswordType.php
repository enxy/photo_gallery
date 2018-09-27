<?php
/**
 * Created by PhpStorm.
 * User: Jolanta
 * Date: 02.09.2017
 * Time: 22:27
 */
namespace Form\user;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class UserPasswordType
 * @package Form
 */
class UserPasswordType extends AbstractType
{
    /**
     * User password form.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'password',
            RepeatedType::class,
            [
                'label' => 'label.password',
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options' => array('label' => 'label.password'),
                'second_options' => array('label' => 'label.repeat_password')
            ]
        );
    }
}