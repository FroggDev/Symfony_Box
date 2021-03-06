<?php
namespace App\Form\Security;

use App\Entity\Author;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserRecoverType
 * @package App\Form
 */
class UserRecoverType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            EmailType::class,
            [
                'required' => true,
                'label' => false,
                    'attr' => [
                    'placeholder' => 'Set your email.'
                    ]
            ]
        )->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Recover my password ',
                'attr' => array('class' => 'btn btn-block btn-primary')
                ]
        )->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
