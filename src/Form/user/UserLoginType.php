<?php
/**
 * Created by PhpStorm.
 * User: Jolanta
 * Date: 02.09.2017
 * Time: 21:55
 */

namespace Form\user;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Class UserLoginType
 * @package Form
 */
class UserLoginType extends AbstractType{
    /**
     * Build login form.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'login',
            TextType::class,
            [
                'label' => 'user.login',
                'required' => true,
                'attr' => [
                    'max_length' => 32,
                    'min_length' => 3,
                ]
            ]
        );
    }
}