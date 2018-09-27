<?php
/**
 * Created by PhpStorm.
 * User: Jolanta
 * Date: 28.05.2017
 * Time: 20:47
 */
namespace Form\user;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegisterType
 * @package Form
 */
class RegisterType extends AbstractType
{
    /**
     * Build form to register users.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=>'firstname.label','constraints' => [
        new Assert\NotBlank(
            ['groups' => ['register-default']]
        ),
        new Assert\Length(
            [
                'groups' => ['register-default'],
                'min' => 3,
                'max' => 128,
            ]
        ),
    ],])
            ->add('surname', TextType::class,['label'=>'lastname.label', 'constraints' => [
                new Assert\NotBlank(
                    ['groups' => ['register-default']]
                ),
                new Assert\Length(
                    [
                        'groups' => ['register-default'],
                        'min' => 3,
                        'max' => 128,
                    ]
                )],])
            ->add('email', TextType::class, ['label'=>'email.label', 'constraints'=>[new Assert\Email([
                'groups' => ['register-default']])
            ]])
            ->add('login', TextType::class,['label'=>'user.login', 'constraints'=>[new Assert\NotBlank(
                ['groups'=>['register-default']]
            ),
                new Assert\Length(
                [
                    'groups'=>['register-default'],
                    'min'=>3,
                    'max'=>128,
                ]
            ),
            ]])->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options' => array('label' => 'label.password'),
                    'second_options' => array('label' => 'label.repeat_password')
                ]
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
                'validation_groups' => 'register-default',
            ]
        );
    }

    /**
     * Get block prefix.
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'register_type';
    }
}