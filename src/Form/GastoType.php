<?php

namespace App\Form;

use App\Entity\Gasto;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GastoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categoria', EntityType::class, array(
                'class'=> 'App\Entity\CategoriaGasto',
                'query_builder'=>function(EntityRepository $er) {
                    return $er->createQueryBuilder('CategoriaGasto');
                },
                'choice_label' => 'nombre',
                'empty_data' => 'null',
            ))
            ->add('importe', NumberType::class, [
                'label'=>'Introduce el importe del gasto (*) (€)'
            ])
            ->add('descripcion', TextType::class, [
                'label'=>'Descripción del gasto (*)'
            ])
            ->add('CrearGasto', type: SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gasto::class,
        ]);
    }
}
