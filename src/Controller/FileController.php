<?php

namespace App\Controller;
use App\Entity\File;
use App\Entity\User;
use App\Entity\Proyectos;
use App\Form\ExcelType;
use App\Form\FileType;
use App\Form\UserType;
use App\Form\UserEmpleadoType;
use App\Form\UserEditType;
use App\Repository\ProyectosRepository;
use App\Repository\SedesRepository;
use App\Repository\UserRepository;
use App\service\FileUploader;
use Mpdf\Tag\Time;
use phpDocumentor\Reflection\Types\Null_;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class FileController extends AbstractController
{
    #[Route('/registroExcel', name: 'registro-excel')]
    function registroExcel(Request $request,  Doctrine\Persistence\ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger, UserRepository $users): Response
    {
        $excel=new File();

        $form = $this->createForm(FileType::class, $excel);
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get('archivo')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('excel_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $excel->setArchivo($newFilename);
                $archivos='C:/xampp/htdocs/Organizacion/public/excelFiles/'.$newFilename;
                $excel= IOFactory::load($archivos);
                $excel ->setActiveSheetIndex(0);
                $filas=$excel->setActiveSheetIndex(0)->getHighestRow();
                for ($i=2; $i<= $filas; $i++){
                    $nombre= $excel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                    if(is_null($nombre)){
                        break;
                    }
                    $apellidos= $excel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                    $sexo= $excel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $fechaNacimiento= $excel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();

                    $pais= $excel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $estadoCivil= $excel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $discapacidad= $excel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                    $documentacionLegal= $excel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $comunidadAutonoma= $excel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                    $password=$this->generatePassword(15);

                    $date = date_create($fechaNacimiento);
                    $fecha= date_format($date, 'Y-m-d H:i:s');
                    $user=new User();
                    $em= $doctrine->getManager();
                    $em->persist($user);
                    $user->setRol("Usuario");
                    $user->setRoles(['ROLE_USER']);
                    $user->setNombre($nombre);
                    $user->setApellidos($apellidos);
                    $user->setSexo($sexo);
                    $user->setFechaNacimiento(new \DateTime($fecha));
                    $user->setNacionalidad($pais);
                    $user->setEstadoCivil($estadoCivil);
                    $user->setDiscapacidad($discapacidad);
                    $user->setDocumentacionLegal($documentacionLegal);
                    $user->setComunidadAutonoma($comunidadAutonoma);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    if(!(is_null($excel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue()))){
                        $user->setImagen($excel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue());
                    }

                    $user->setFechaAlta(new \DateTime());
                    $edad=$user->obtener_edad_segun_fecha($user->getFechaNacimiento());
                    $user->setEdad($edad);

                    $email= "$nombre"."_"."$apellidos"."_"."$comunidadAutonoma"."_"."$edad" . "@usuario.org.es";
                    $email = str_replace(" ", "", $email);
                    $email = strtolower($email);
                    $email = $this->eliminar_acentos($email);
                    $existeEmail=$users->findBy(array('email' => $email));
                    if($existeEmail){
                        $email= "$nombre"."_"."$apellidos"."_"."$comunidadAutonoma"."_"."$edad" ."_2@usuario.org.es";
                        $email = str_replace(" ", "", $email);
                        $email = strtolower($email);
                        $email = $this->eliminar_acentos($email);
                    }
                    $user->setEmail($email);
                    $em->flush();



                }
                unlink($archivos);
                $this->addFlash('exitoUser2','Se han registrado los usuarios exitosamente');
                return $this->redirectToRoute('dashboard_todosUsuarios');

            }
        }
        return $this->render('registroExcel.html.twig',[
            'formulario' => $form->createView()]);
    }


    private function generatePassword($length)
    {
        $key = "";
        $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
        $max = strlen($pattern)-1;
        for($i = 0; $i < $length; $i++){
            $key .= substr($pattern, mt_rand(0,$max), 1);
        }
        return $key;
    }
    private function eliminar_acentos($cadena){

        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena );

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena );

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena );

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena );

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );

        return $cadena;
    }
}