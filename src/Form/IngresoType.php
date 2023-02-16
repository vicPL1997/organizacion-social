<?php

namespace App\Form;

use App\Entity\Ingreso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngresoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tipo', ChoiceType::class,[
                'label' => "Tipo de subvención (*)",
                'choices' => [
                    "Administración pública" => "1",
                    "Entidad privada" => "2",
                    "Fondos propios" => "3",
                ]] )
            ->add('nombreEmisor', TextType::class, [
                'label' => "Emisor de la subvención (*)",
            ])
            ->add('cantidad', NumberType::class, [
                'label'=>'Cantidad de la subvención (*) (€)'
            ])
            ->add('CrearIngreso', type: SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingreso::class,
        ]);
    }
}
