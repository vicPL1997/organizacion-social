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

class RegistroController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_SEDES')")
     */
    #[Route('/registro', name: 'app_registro')]
    public function index(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger, UserRepository $users): Response
    {

        $user=new User();

        $form = $this->createForm(UserType::class, $user);
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em= $doctrine->getManager();
            $em->persist($user);
            $rol = $user->getRol();
            if($rol == "Administrador"){
                $user->setRoles(['ROLE_ADMIN']);
            }elseif($rol == "Usuario"){
                $user->setRoles(['ROLE_USER']);
            }elseif($rol == "Voluntario"){
                $user->setRoles(['ROLE_VOLUNTARIO']);
            }elseif($rol == "Administrador de sede"){
                $user->setRoles(['ROLE_ADMIN_SEDES']);
                $user->setTieneSedeAdministrada(0);
            }
            $user->setFechaAlta(new \DateTime());
            $edad=$user->obtener_edad_segun_fecha($user->getFechaNacimiento());
            $user->setEdad($edad);
            $nombre=$user->getNombre();

            $apellido=$user->getApellidos();
            $comunidadAutonoma=$user->getComunidadAutonoma();
            if($rol == "Usuario"){
                $email= "$nombre"."_"."$apellido"."_"."$comunidadAutonoma"."_"."$edad" . "@usuario.org.es";
                $email = str_replace(" ", "", $email);
                $email = strtolower($email);
                $email = $this->eliminar_acentos($email);
                $existeEmail=$users->findBy(array('email' => $email));
                if($existeEmail){
                    $email= "$nombre"."_"."$apellido"."_"."$comunidadAutonoma"."_"."$edad" ."_2@usuario.org.es";
                    $email = str_replace(" ", "", $email);
                    $email = strtolower($email);
                    $email = $this->eliminar_acentos($email);
                }
            }elseif($rol == "Voluntario"){
                $email= "$nombre"."_"."$apellido"."_"."$comunidadAutonoma" . "@voluntario.org.es";
                $email = str_replace(" ", "", $email);
                $email = strtolower($email);
                $email = $this->eliminar_acentos($email);
                $existeEmail=$users->findBy(array('email' => $email));
                if($existeEmail){
                    $email= "$nombre"."_"."$apellido"."_"."$comunidadAutonoma"."_2@voluntario.org.es";
                    $email = str_replace(" ", "", $email);
                    $email = strtolower($email);
                    $email = $this->eliminar_acentos($email);
                }
            }else{
                $email= "$nombre"."_"."$apellido"."_". "$comunidadAutonoma" . "@org.es";
                $email = str_replace(" ", "", $email);
                $email = strtolower($email);
                $email = $this->eliminar_acentos($email);
                $existeEmail=$users->findBy(array('email' => $email));
                if($existeEmail){
                    $email= "$nombre"."_"."$apellido"."_"."$comunidadAutonoma" . "_2@org.es";
                    $email = str_replace(" ", "", $email);
                    $email = strtolower($email);
                    $email = $this->eliminar_acentos($email);
                }
            }
            $user->setEmail($email);
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            $imgFile = $form->get('imagen')->getData();
            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();
                try {
                    $imgFile->move(
                        $this->getParameter('imagen_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setImagen($newFilename);
            }


            $em->flush();
            $this->addFlash('exitoUser','Se ha regisrado el usuario exitosamente');
            return $this->redirectToRoute('dashboard_todosUsuarios');

        }


        return $this->render('registro/index.html.twig', [
            'formulario' => $form->createView(),
        ]);
    }

    //Registrar empleado
    #[Route('/registro-empleado', name: 'app_registro_empleado')]
    public function registrarEmpleado(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
    {
        $user=new User();

        $form = $this->createForm(UserEmpleadoType::class, $user);
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em= $doctrine->getManager();
            $em->persist($user);
            $user->setRol("Empleado");
            $user->setRoles(['ROLE_EMPLEADO']);
            $user->setFechaAlta(new \DateTime());
            $edad=$user->obtener_edad_segun_fecha($user->getFechaNacimiento());
            $user->setEdad($edad);
            $nombre=$user->getNombre();
            $apellido=$user->getApellidos();
            $comunidadAutonoma=$user->getComunidadAutonoma();
            $email= "$nombre"."_"."$apellido"."_"."$comunidadAutonoma" . "@empleado.org.es";
            $email = str_replace(" ", "", $email);
            $email = strtolower($email);
            $email = $this->eliminar_acentos($email);
            $user->setEmail($email);
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $imgFile = $form->get('imagen')->getData();
            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();
                try {
                    $imgFile->move(
                        $this->getParameter('imagen_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setImagen($newFilename);
            }

            $em->flush();
            $this->addFlash('exitoUser','Se ha regisrado el usurio exitosamente');
            return $this->redirectToRoute('dashboard_todosUsuarios');

        }


        return $this->render('registro/empleado.html.twig', [
            'formulario' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_SEDES')")
     */
    #[Route('/editarUsuario/{id}/{tab}/{idProyecto}', methods: ['GET', 'POST'], name: 'editar_usuario')]
    public function edit(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, User $user, SluggerInterface $slugger, $tab=0, $idProyecto=""): Response
    {
        if($user->getRol()=="Empleado"){
            $form = $this->createForm(UserEmpleadoType::class, $user);
        }else{
            $form = $this->createForm(UserEditType::class, $user);
        }
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $rol=$user->getRol();
            if($rol == "Administrador"){
                $user->setRoles(['ROLE_ADMIN']);
            }elseif($rol == "Usuario"){
                $user->setRoles(['ROLE_USER']);
            }elseif($rol == "Voluntario"){
                $user->setRoles(['ROLE_VOLUNTARIO']);
            }elseif($rol == "Administrador de sede"){
                $user->setRoles(['ROLE_ADMIN_SEDES']);
            }elseif($rol == "Empleado"){
                $user->setRoles(['ROLE_EMPLEADO']);
            }
            $edad=$user->obtener_edad_segun_fecha($user->getFechaNacimiento());
            $user->setEdad($edad);
            $imgFile = $form->get('imagen')->getData();
            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();
                try {
                    $imgFile->move(
                        $this->getParameter('imagen_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setImagen($newFilename);
            }
            $em->flush();
            $this->addFlash('exitoUserEdit','Se ha editado el usuario exitosamente');

            if($tab==0){
                return $this->redirectToRoute('dashboard_todosUsuarios');
            }elseif($tab==2){
                return $this->redirectToRoute('verProyecto', ['id'=>$idProyecto, 'tab'=>2]);
            }elseif($tab==3){
                return $this->redirectToRoute('verProyecto', ['id'=>$idProyecto, 'tab'=>3]);
            }elseif($tab==4){
                return $this->redirectToRoute('verProyecto', ['id'=>$idProyecto, 'tab'=>4]);
            }

        }
        return $this->render('registro/editar.html.twig', [
            'formulario' => $form->createView(),
            'usuario'=> $user,
        ]);

    }
    #[Route('/usuario/{id}', name: 'verUsuario')]
    public function verUsuario($id, UserRepository $usuarios){
        $usuario = $usuarios ->find($id);
        $rol=$usuario->getRol();
        if( $rol == "Administrador de sede"){
            $sede = $usuario->getSede();
            if($sede){
                $proyectos=$sede->getProyectos();
            }else{
                $proyectos=null;
            }
        }else{
            if($rol == "Administrador"){
                $proyectos=null;
                $sede=null;
            }else{
                $proyectos = $usuario->getProyectos();
                $sede=null;
            }


        }

        return $this->render('dashboard/verUsuario.html.twig', [
            'ac' => $usuario,
            'proyectos' => $proyectos,
            'sede' => $sede,
            'rol'=> $rol

        ]);
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
    #[Route('/probandojeje', name: 'probando')]
    public function pruebaaadsdsdsd(){
        $paises = ["Afganistán","Albania","Alemania","Andorra","Angola","Antigua y Barbuda","Arabia Saudita","Argelia","Argentina","Armenia","Australia","Austria","Azerbaiyán","Bahamas","Bangladés","Barbados","Baréin","Bélgica","Belice","Benín","Bielorrusia","Birmania","Bolivia","Bosnia y Herzegovina","Botsuana","Brasil","Brunéi","Bulgaria","Burkina Faso","Burundi","Bután","Cabo Verde","Camboya","Camerún","Canadá","Catar","Chad","Chile","China","Chipre","Ciudad del Vaticano","Colombia","Comoras","Corea del Norte","Corea del Sur","Costa de Marfil","Costa Rica","Croacia","Cuba","Dinamarca","Dominica","Ecuador","Egipto","El Salvador","Emiratos Árabes Unidos","Eritrea","Eslovaquia","Eslovenia","España","Estados Unidos","Estonia","Etiopía","Filipinas","Finlandia","Fiyi","Francia","Gabón","Gambia","Georgia","Ghana","Granada","Grecia","Guatemala","Guyana","Guinea","Guinea ecuatorial","Guinea-Bisáu","Haití","Honduras","Hungría","India","Indonesia","Irak","Irán","Irlanda","Islandia","Islas Marshall","Islas Salomón","Israel","Italia","Jamaica","Japón","Jordania","Kazajistán","Kenia","Kirguistán","Kiribati","Kuwait","Laos","Lesoto","Letonia","Líbano","Liberia","Libia",
            "Liechtenstein","Lituania","Luxemburgo","Madagascar","Malasia","Malaui","Maldivas","Malí","Malta","Marruecos","Mauricio",
            "Mauritania","México","Micronesia","Moldavia","Mónaco","Mongolia","Montenegro","Mozambique","Namibia","Nauru","Nepal","Nicaragua","Níger","Nigeria","Noruega","Nueva Zelanda","Omán","Países Bajos","Pakistán","Palaos","Palestina","Panamá","Papúa Nueva Guinea","Paraguay","Perú","Polonia","Portugal","Reino Unido","República Centroafricana","República Checa","República de Macedonia","República del Congo","República Democrática del Congo","República Dominicana","República Sudafricana","Ruanda","Rumanía","Rusia","Samoa","San Cristóbal y Nieves","San Marino","San Vicente y las Granadinas","Santa Lucía","Santo Tomé y Príncipe","Senegal","Serbia","Seychelles","Sierra Leona","Singapur","Siria","Somalia","Sri Lanka","Suazilandia","Sudán","Sudán del Sur","Suecia","Suiza","Surinam","Tailandia","Tanzania","Tayikistán","Timor Oriental","Togo","Tonga","Trinidad y Tobago","Túnez","Turkmenistán","Turquía","Tuvalu","Ucrania","Uganda","Uruguay","Uzbekistán","Vanuatu","Venezuela","Vietnam","Yemen","Yibuti","Zambia","Zimbabue"];
        for($i = 0 ;$i<sizeof($paises);$i++){
            echo "\"".$paises[$i]."\""."=>"."\"".$paises[$i]."\"".", ";

        }
        return $this->render('registro/registro-excel.html.twig', [

        ]);
    }

}

