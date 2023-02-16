<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('archivo', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Excel (xlsx)(*):',
                'required' => true,
                'attr'  => array('placeholder'=> 'Seleccione el archivo excel...')

            ])
            ->add('NuevoArchivo', SubmitType::class, [
                'label' => "Añadir usuarios",
                'attr' => ['class' => 'btn btn-orange border-radius-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
