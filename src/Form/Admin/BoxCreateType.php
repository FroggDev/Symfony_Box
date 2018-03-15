<?php
namespace App\Form\Admin;

use App\Entity\Box;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BoxSubscribeType
 * @package App\Form\Box
 */
class BoxCreateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Name',
                'required' => true
            ]
        )->add(
            'description',
            TextareaType::class,
            [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description...',
                ]
            ]
        )->add(
            'featured_image',
            FileType::class,
            [
                'data_class' => null,
                'required' => false,
                'attr' => [
                    'class' => 'dropify',
                    'data-allowed-file-extensions' => 'gif jpg jpeg png',
                    'data-max-file-size-preview' => '300K'
                ]
            ]
        )->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Confirm',
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
            'data_class' => Box::class
        ]);
    }
}
