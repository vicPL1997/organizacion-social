<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $paises = array("Afganistán","Albania","Alemania","Andorra","Angola","Antigua y Barbuda","Arabia Saudita","Argelia","Argentina","Armenia","Australia","Austria","Azerbaiyán","Bahamas","Bangladés","Barbados","Baréin","Bélgica","Belice","Benín","Bielorrusia","Birmania","Bolivia","Bosnia y Herzegovina","Botsuana","Brasil","Brunéi","Bulgaria","Burkina Faso","Burundi","Bután","Cabo Verde","Camboya","Camerún","Canadá","Catar","Chad","Chile","China","Chipre","Ciudad del Vaticano","Colombia","Comoras","Corea del Norte","Corea del Sur","Costa de Marfil","Costa Rica","Croacia","Cuba","Dinamarca","Dominica","Ecuador","Egipto","El Salvador","Emiratos Árabes Unidos","Eritrea","Eslovaquia","Eslovenia","España","Estados Unidos","Estonia","Etiopía","Filipinas","Finlandia","Fiyi","Francia","Gabón","Gambia","Georgia","Ghana","Granada","Grecia","Guatemala","Guyana","Guinea","Guinea ecuatorial","Guinea-Bisáu","Haití","Honduras","Hungría","India","Indonesia","Irak","Irán","Irlanda","Islandia","Islas Marshall","Islas Salomón","Israel","Italia","Jamaica","Japón","Jordania","Kazajistán","Kenia","Kirguistán","Kiribati","Kuwait","Laos","Lesoto","Letonia","Líbano","Liberia","Libia","Liechtenstein","Lituania","Luxemburgo","Madagascar","Malasia","Malaui","Maldivas","Malí","Malta","Marruecos","Mauricio","Mauritania","México","Micronesia","Moldavia","Mónaco","Mongolia","Montenegro","Mozambique","Namibia","Nauru","Nepal","Nicaragua","Níger","Nigeria","Noruega","Nueva Zelanda","Omán","Países Bajos","Pakistán","Palaos","Palestina","Panamá","Papúa Nueva Guinea","Paraguay","Perú","Polonia","Portugal","Reino Unido","República Centroafricana","República Checa","República de Macedonia","República del Congo","República Democrática del Congo","República Dominicana","República Sudafricana","Ruanda","Rumanía","Rusia","Samoa","San Cristóbal y Nieves","San Marino","San Vicente y las Granadinas","Santa Lucía","Santo Tomé y Príncipe","Senegal","Serbia","Seychelles","Sierra Leona","Singapur","Siria","Somalia","Sri Lanka","Suazilandia","Sudán","Sudán del Sur","Suecia","Suiza","Surinam","Tailandia","Tanzania","Tayikistán","Timor Oriental","Togo","Tonga","Trinidad y Tobago","Túnez","Turkmenistán","Turquía","Tuvalu","Ucrania","Uganda","Uruguay","Uzbekistán","Vanuatu","Venezuela","Vietnam","Yemen","Yibuti","Zambia","Zimbabue");
        $builder
            ->add('nombre', TextType::class, [
                'attr' => array(
                    'placeholder' => ' '),
                'required' => true,
            ])
            ->add('apellidos', TextType::class, [
                'attr' => array(
                    'placeholder' => ' '),
                'required' => true,
            ])
            ->add('email', EmailType::class,[
                'required' => true,
            ])
            ->add('sexo', ChoiceType::class, [
                'label' => "Sexo",
                'choices' => [
                    "Masculino" => "Masculino",
                    "Femenino" => "Femenino",
                ]])
            ->add('rol', ChoiceType::class, [
                'label' => "Rol del usuario",
                'choices' => [
                    "Usuario" => "Usuario",
                    "Administrador de sede" => "Administrador de sede",
                    "Voluntario" => "Voluntario",
                ]])

            ->add('fechaNacimiento', DateType::class, [
                'label' => 'Fecha de nacimiento',
                'widget' => 'single_text',
                'required' => true,
                'trim'    => true,
            ])
            ->add('discapacidad', ChoiceType::class,[
                'label' => "Discapacidad",
                'choices' => [
                    "Si" => "Si",
                    "No" => "No",
                ],
                'preferred_choices' => ['No']
            ] )
            ->add('documentacionLegal', ChoiceType::class,[
                'label' => "Documentación legal",
                'choices' => [
                    "Si" => "Si",
                    "No" => "No",


                ]] )
            ->add('nacionalidad', ChoiceType::class,[
                'label' => "País",
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    "Afganistán"=>"Afganistán", "Albania"=>"Albania", "Alemania"=>"Alemania", "Andorra"=>"Andorra", "Angola"=>"Angola", "Antigua y Barbuda"=>"Antigua y Barbuda", "Arabia Saudita"=>"Arabia Saudita", "Argelia"=>"Argelia", "Argentina"=>"Argentina", "Armenia"=>"Armenia", "Australia"=>"Australia", "Austria"=>"Austria", "Azerbaiyán"=>"Azerbaiyán", "Bahamas"=>"Bahamas", "Bangladés"=>"Bangladés", "Barbados"=>"Barbados", "Baréin"=>"Baréin", "Bélgica"=>"Bélgica", "Belice"=>"Belice", "Benín"=>"Benín", "Bielorrusia"=>"Bielorrusia", "Birmania"=>"Birmania", "Bolivia"=>"Bolivia", "Bosnia y Herzegovina"=>"Bosnia y Herzegovina", "Botsuana"=>"Botsuana", "Brasil"=>"Brasil", "Brunéi"=>"Brunéi", "Bulgaria"=>"Bulgaria", "Burkina Faso"=>"Burkina Faso", "Burundi"=>"Burundi", "Bután"=>"Bután", "Cabo Verde"=>"Cabo Verde", "Camboya"=>"Camboya", "Camerún"=>"Camerún", "Canadá"=>"Canadá", "Catar"=>"Catar", "Chad"=>"Chad", "Chile"=>"Chile", "China"=>"China", "Chipre"=>"Chipre", "Ciudad del Vaticano"=>"Ciudad del Vaticano", "Colombia"=>"Colombia", "Comoras"=>"Comoras", "Corea del Norte"=>"Corea del Norte", "Corea del Sur"=>"Corea del Sur", "Costa de Marfil"=>"Costa de Marfil", "Costa Rica"=>"Costa Rica", "Croacia"=>"Croacia", "Cuba"=>"Cuba", "Dinamarca"=>"Dinamarca", "Dominica"=>"Dominica", "Ecuador"=>"Ecuador", "Egipto"=>"Egipto", "El Salvador"=>"El Salvador", "Emiratos Árabes Unidos"=>"Emiratos Árabes Unidos", "Eritrea"=>"Eritrea", "Eslovaquia"=>"Eslovaquia", "Eslovenia"=>"Eslovenia", "España"=>"España", "Estados Unidos"=>"Estados Unidos", "Estonia"=>"Estonia", "Etiopía"=>"Etiopía", "Filipinas"=>"Filipinas", "Finlandia"=>"Finlandia", "Fiyi"=>"Fiyi", "Francia"=>"Francia", "Gabón"=>"Gabón", "Gambia"=>"Gambia", "Georgia"=>"Georgia", "Ghana"=>"Ghana", "Granada"=>"Granada", "Grecia"=>"Grecia", "Guatemala"=>"Guatemala", "Guyana"=>"Guyana", "Guinea"=>"Guinea", "Guinea ecuatorial"=>"Guinea ecuatorial", "Guinea-Bisáu"=>"Guinea-Bisáu", "Haití"=>"Haití", "Honduras"=>"Honduras", "Hungría"=>"Hungría", "India"=>"India", "Indonesia"=>"Indonesia", "Irak"=>"Irak", "Irán"=>"Irán", "Irlanda"=>"Irlanda", "Islandia"=>"Islandia", "Islas Marshall"=>"Islas Marshall", "Islas Salomón"=>"Islas Salomón", "Israel"=>"Israel", "Italia"=>"Italia", "Jamaica"=>"Jamaica", "Japón"=>"Japón", "Jordania"=>"Jordania", "Kazajistán"=>"Kazajistán", "Kenia"=>"Kenia", "Kirguistán"=>"Kirguistán", "Kiribati"=>"Kiribati", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesoto"=>"Lesoto", "Letonia"=>"Letonia", "Líbano"=>"Líbano", "Liberia"=>"Liberia", "Libia"=>"Libia", "Liechtenstein"=>"Liechtenstein", "Lituania"=>"Lituania", "Luxemburgo"=>"Luxemburgo", "Madagascar"=>"Madagascar", "Malasia"=>"Malasia", "Malaui"=>"Malaui", "Maldivas"=>"Maldivas", "Malí"=>"Malí", "Malta"=>"Malta", "Marruecos"=>"Marruecos", "Mauricio"=>"Mauricio", "Mauritania"=>"Mauritania", "México"=>"México", "Micronesia"=>"Micronesia", "Moldavia"=>"Moldavia", "Mónaco"=>"Mónaco", "Mongolia"=>"Mongolia", "Montenegro"=>"Montenegro", "Mozambique"=>"Mozambique", "Namibia"=>"Namibia", "Nauru"=>"Nauru", "Nepal"=>"Nepal", "Nicaragua"=>"Nicaragua", "Níger"=>"Níger", "Nigeria"=>"Nigeria", "Noruega"=>"Noruega", "Nueva Zelanda"=>"Nueva Zelanda", "Omán"=>"Omán", "Países Bajos"=>"Países Bajos", "Pakistán"=>"Pakistán", "Palaos"=>"Palaos", "Palestina"=>"Palestina", "Panamá"=>"Panamá", "Papúa Nueva Guinea"=>"Papúa Nueva Guinea", "Paraguay"=>"Paraguay", "Perú"=>"Perú", "Polonia"=>"Polonia", "Portugal"=>"Portugal", "Reino Unido"=>"Reino Unido", "República Centroafricana"=>"República Centroafricana", "República Checa"=>"República Checa", "República de Macedonia"=>"República de Macedonia", "República del Congo"=>"República del Congo", "República Democrática del Congo"=>"República Democrática del Congo", "República Dominicana"=>"República Dominicana", "República Sudafricana"=>"República Sudafricana", "Ruanda"=>"Ruanda", "Rumanía"=>"Rumanía", "Rusia"=>"Rusia", "Samoa"=>"Samoa", "San Cristóbal y Nieves"=>"San Cristóbal y Nieves", "San Marino"=>"San Marino", "San Vicente y las Granadinas"=>"San Vicente y las Granadinas", "Santa Lucía"=>"Santa Lucía", "Santo Tomé y Príncipe"=>"Santo Tomé y Príncipe", "Senegal"=>"Senegal", "Serbia"=>"Serbia", "Seychelles"=>"Seychelles", "Sierra Leona"=>"Sierra Leona", "Singapur"=>"Singapur", "Siria"=>"Siria", "Somalia"=>"Somalia", "Sri Lanka"=>"Sri Lanka", "Suazilandia"=>"Suazilandia", "Sudán"=>"Sudán", "Sudán del Sur"=>"Sudán del Sur", "Suecia"=>"Suecia", "Suiza"=>"Suiza", "Surinam"=>"Surinam", "Tailandia"=>"Tailandia", "Tanzania"=>"Tanzania", "Tayikistán"=>"Tayikistán", "Timor Oriental"=>"Timor Oriental", "Togo"=>"Togo", "Tonga"=>"Tonga", "Trinidad y Tobago"=>"Trinidad y Tobago", "Túnez"=>"Túnez", "Turkmenistán"=>"Turkmenistán", "Turquía"=>"Turquía", "Tuvalu"=>"Tuvalu", "Ucrania"=>"Ucrania", "Uganda"=>"Uganda", "Uruguay"=>"Uruguay", "Uzbekistán"=>"Uzbekistán", "Vanuatu"=>"Vanuatu", "Venezuela"=>"Venezuela", "Vietnam"=>"Vietnam", "Yemen"=>"Yemen", "Yibuti"=>"Yibuti", "Zambia"=>"Zambia", "Zimbabue"=>"Zimbabue"
                ]])
            ->add('estadoCivil', ChoiceType::class,[
                'label' => "Estado civil",
                'choices' => [
                    "Soltero" => "Soltero",
                    "Casado" => "Casado",
                ]] )
            ->add('imagen', FileType::class, [
                'label' => 'Imagen (jpg)',
                 'data_class' => null,
                'required' => false,
                'attr'  => array('placeholder'=> 'Seleccione una imagen...')

            ])
            ->add('EditarUsuario', type: SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
