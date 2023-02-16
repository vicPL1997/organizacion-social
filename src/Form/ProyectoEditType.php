<?php

namespace App\Form;

use App\Entity\Proyectos;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProyectoEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('zonaActuacion', TextType::class, [
                'label' => "Zona de actuación (*)",
                'attr' => array(
                    'placeholder' => 'Lugar dentro de la localización de la sede donde se llevará a cabo el proyecto'
                )
            ])
            ->add('fechaInicio',DateType::class, [
                'label' => 'Fecha de inicio del proyecto (*)',
                'widget' => 'single_text',
                'required' => true,
                'trim'    => true,
            ])
            ->add('fechaFinal',DateType::class , [
                'label' => 'Fecha de fin del proyecto (*)',
                'widget' => 'single_text',
                'required' => true,
                'trim'    => true,
            ])
            ->add('activo', ChoiceType::class,[
                'label' => "Actividad",
                'choices' => [
                    "Activo" => "Activo",
                    "Inactivo" => "Inactivo",
                ]] )

            ->add('EditarProyecto', type: SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proyectos::class,
        ]);
    }
}
