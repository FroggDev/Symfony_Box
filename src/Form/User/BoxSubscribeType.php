<?php
namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BoxSubscribeType
 * @package App\Form\User
 */
class BoxSubscribeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'hasSubscribe',
            CheckboxType::class,
            [
                'label' => 'Subscribe',
                'required' => false
            ]
        )->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Validate',
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
