<?php

namespace App\Form;

use App\Entity\Sedes;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SedeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $comunidades = ["Andalucía", "Aragón", "Canarias", "Cantabria", "Castilla y León", "Castilla-La Mancha", "Cataluña", "Ceuta", "Comunidad Valenciana", "Comunidad de Madrid",
            "Extremadura", "Galicia", "Islas Baleares", "La Rioja", "Melilla", "Navarra", "País Vasco", "Principado de Asturias", "Región de Murcia"];

        $builder
            ->add('nombre')
            ->add('localizacion', ChoiceType::class, [
                'label' => "Comunidad autónoma",
                'choices' => [

                    "Andalucia" => $comunidades[0],
                    "Aragón" => $comunidades[1],
                    "Canarias" => $comunidades[2],
                    "Cantabria" => $comunidades[3],
                    "Castilla y León" => $comunidades[4],
                    "Castilla-La Mancha" => $comunidades[5],
                    "Cataluña" => $comunidades[6],
                    "Ceuta" => $comunidades[7],
                    "Comunidad Valenciana" => $comunidades[8],
                    "Comunidad de Madrid" => $comunidades[9],
                    "Extremadura" => $comunidades[10],
                    "Galicia" => $comunidades[11],
                    "Islas Baleares" => $comunidades[12],
                    "La Rioja" => $comunidades[13],
                    "Melilla" => $comunidades[14],
                    "Navarra" => $comunidades[15],
                    "País Vasco" => $comunidades[16],
                    "Principado de Asturias" => $comunidades[17],
                    "Región de Murcia" => $comunidades[18]
                ]])
            ->add('administradorSede', EntityType::class, array(
                'class'=> 'App\Entity\User',
                'query_builder'=>function(EntityRepository $er) {
                    return $er->createQueryBuilder('user')->where('user.rol = :roll', 'user.tieneSedeAdministrada = :sede')
                        ->setParameter('roll','Administrador de sede')
                        ->setParameter('sede', 0);

                },
                'empty_data' => 'null',
            ))
            ->add('NuevaSede', type: SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sedes::class,
        ]);
    }
}
