<?php

namespace App\Controller;

use App\Entity\Proyectos;
use App\Entity\Sedes;
use App\Entity\User;
use App\Form\ProyectoEditType;
use App\Form\ProyectoType;
use App\Form\SedeType;
use App\Form\UserEditType;
use App\Repository\GastoRepository;
use App\Repository\IngresoRepository;
use App\Repository\ProyectosRepository;
use App\Repository\SedesRepository;
use App\Repository\UserRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use DateTime;



class ProyectosController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN_SEDES")
     */
    #[Route('/crearProyecto', name: 'crear_proyecto')]
    public function index(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, SedesRepository $sedes, ProyectosRepository $proyectos)
    {
        $proyecto= new Proyectos();
        $form= $this->createForm(ProyectoType::class, $proyecto);
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em= $doctrine->getManager();
            $em->persist($proyecto);
            $user = $this->getUser();
            $idUser= $user->getId();
            $sede=$sedes->findOneBy(array('administradorSede' => $idUser));
            $proyecto->setSede($sede);
            $proyecto->setTotalVoluntarios(0);
            $proyecto->setPersonalVinculado(0);
            $proyecto->setTotalParticipantes(0);
            $proyecto->setTotalGasto(0);
            $proyecto->setIngresosTotales(0);
            $fechaInicio=$proyecto->getFechaInicio();
            $fechaHoy = new DateTime(date("Y-m-d"));
            if($fechaHoy>=$fechaInicio){
                $proyecto->setActivo("Activo");
            }else{
                $proyecto->setActivo("Inactivo");
            }

            $em->flush();

            $this->addFlash('exito3','Se ha creado el proyecto correctamente');
            return $this->redirectToRoute('dashboard_Misede');
        }
        return $this->render('proyectos/index.html.twig', [
            'controller_name' => 'ProyectosController',
            'form' => $form->createView()

        ]);
    }
    /**
     * @IsGranted("ROLE_ADMIN_SEDES")
     */
    #[Route('/editarProyecto/{id}', name: 'editar_proyecto')]
    public function editarProyecto(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, SedesRepository $sedes, Proyectos $proyecto){
        $form = $this->createForm(ProyectoEditType::class, $proyecto);
        $form -> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            $this->addFlash('exitoProyecto','Se ha editado el proyecto exitosamente');
            return $this->redirectToRoute('dashboard_Misede');
        }
        return $this->render('proyectos/editar.html.twig', [
            'formulario' => $form->createView(),
            'proyecto'=> $proyecto,
        ]);
    }
    #[Route('/proyecto/{id}/{tab}', name: 'verProyecto')]
    public function verProyecto(ChartBuilderInterface $chartBuilder,$id, ProyectosRepository $proyectos, UserRepository $users, $tab=1 ){

        $proyecto = $proyectos ->find($id);
        $usuarios=$users->verUsuariosDelProyecto($id);
        $usuariosBeneficiarios=$users->findBy(array('rol' => 'Usuario', 'comunidadAutonoma' => $proyecto->getSede()->getLocalizacion()));

        $adminSede=$proyecto->getSede()->getAdministradorSede()->getId();
        $voluntarios=$users->verVoluntariosDelProyecto($id);
        $usuariosVoluntarios=$users->findBy(array('rol' => 'Voluntario', 'comunidadAutonoma' => $proyecto->getSede()->getLocalizacion()));

        $empleados=$users->verEmpleadosDelProyecto($id);
        $usuariosEmpleados=$users->findBy(array('rol' => 'Empleado', 'comunidadAutonoma' => $proyecto->getSede()->getLocalizacion()));


        $gastos=$proyecto->getGastos();
        $ingresos=$proyecto->getIngresos();
        $ingresosTotales=0;
        foreach($ingresos as $ingreso){
            $ingresosTotales += $ingreso->getCantidad();
        }
        $gastoTotalSinEmpleados=0;
        foreach($gastos as $gasto){
            $gastoTotalSinEmpleados += $gasto->getImporte();
        }
        $gastoEmpleados=0;
        foreach($empleados as $empleado){
            $gastoEmpleados=$gastoEmpleados+$empleado['sueldo_empleado'];
        }
        $gastoTotal=$gastoTotalSinEmpleados+$gastoEmpleados;
        if(!(empty($usuarios))){
            $chart=self::estadisticasGenero($chartBuilder, self::obtenerPorcentajeSexo($usuarios, "Masculino"), self::obtenerPorcentajeSexo($usuarios, "Femenino"));
            $chart3=self::porcentajeEdad($chartBuilder, $usuarios);
            $chart4=self::estadisticasEstadoCivil($chartBuilder, $usuarios);
            $pHombres=round(self::obtenerPorcentajeSexo($usuarios, "Masculino"), 2);
            $pMujeres = round(self::obtenerPorcentajeSexo($usuarios, "Femenino"), 2);
            $chart5=self::porcentajeNacionalidades($chartBuilder, $usuarios);
            $chart6=self::estadisticasPapelesEnRegla($chartBuilder, $usuarios);
            $chart7=self::estadisticasDiscapacidad($chartBuilder, $usuarios);

        }else{
            $chart=null;
            $chart3=null;
            $chart4=null;
            $chart5=null;
            $chart6=null;
            $chart7=null;
            $pHombres=null;
            $pMujeres=null;
        }
        if(sizeof($gastos) > 0){
            $chart8=self::estadisticasGastos($chartBuilder, $gastos, $gastoTotal, $gastoEmpleados);
        }else{
            $chart8=null;

        }
        if(sizeof($ingresos) > 0) {
            $chart9 = self::estadisticasIngresos($chartBuilder, $ingresos, $ingresosTotales);
        }else{
            $chart9=null;
        }
        $proyecto->setTotalGasto($gastoTotal);


            return $this->render('proyectos/verProyecto.html.twig', [
            'proyecto' => $proyecto, 'usuarios'=>$usuarios, 'voluntarios' => $voluntarios, 'gastos' => $gastos, 'ingresos'=>$ingresos, 'ingresosTotales'=>$ingresosTotales,
            'gastoTotal' => $gastoTotal, 'usuariosBeneficiarios'=>$usuariosBeneficiarios, 'gastoTotalEmpleados'=>$gastoEmpleados,
            'usuariosVoluntarios'=>$usuariosVoluntarios, 'idProyecto'=>$id, 'tab'=>$tab,
            'adminSede'=>$adminSede,
            'Phombres'=>$pHombres,
            'Pmujeres' => $pMujeres,
            'chart' => $chart, 'chart3'=>$chart3, 'chart4'=>$chart4, 'chart5'=>$chart5, 'chart6'=>$chart6,'chart7'=>$chart7,'chart8'=>$chart8,'chart9'=>$chart9,
            'empleados' => $empleados, 'usuariosEmpleados' => $usuariosEmpleados

        ]);
    }
    /**
     * @Route("/proyecto-add-usuario", options={"expose"=true}, methods={"POST"}, name="add_user")
     */
    Public function addUser(Request $request, UserRepository $usuarios, ProyectosRepository $proyectos, Doctrine\Persistence\ManagerRegistry $doctrine){
        if ($request->isXmlHttpRequest()){
            $idUsuario=$request->get('idUsuario');
            $idProyecto=$request->get('idProyecto');
            $em= $doctrine->getManager();
            $usuario=$usuarios->find(array('id' => $idUsuario));
            $proyecto=$proyectos->find(array('id' => $idProyecto));
            $proyecto->addUser($usuario);
            if($usuario->getRol() == "Usuario"){
                $proyecto->setTotalParticipantes($proyecto->getTotalParticipantes()+1);
            }elseif ($usuario->getRol() == "Voluntario"){
                $proyecto->setTotalVoluntarios($proyecto->getTotalVoluntarios()+1);
            }elseif ($usuario->getRol() == "Empleado"){
                $sueldo=$usuario->getSueldoEmpleado();
                $proyecto->setTotalGasto($proyecto->getTotalGasto()+$sueldo);
                $proyecto->setPersonalVinculado($proyecto->getPersonalVinculado()+1);
            }
            $em->flush();
            return new JsonResponse(['nombre' =>$usuario->getNombre()]);
        }else{
            throw new \Exception('No se ha podido añadir el usuario');
        }
    }

    /**
     * @Route("/eliminar_usuario_proyecto", options={"expose"=true}, methods={"POST"}, name="eliminar_usuario_proyecto")
     */
    public function eliminarUsuarioDelProyecto(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, UserRepository $usuarios, ProyectosRepository $proyectos){
        if ($request->isXmlHttpRequest()){
            $idUsuario=$request->get('idUsuario');
            $idProyecto=$request->get('idProyecto');
            $em= $doctrine->getManager();
            $usuario=$usuarios->find(array('id' => $idUsuario));
            $proyecto=$proyectos->find(array('id' => $idProyecto));
            $proyecto->removeUser($usuario);
            if($usuario->getRol() == "Usuario"){
                $proyecto->setTotalParticipantes($proyecto->getTotalParticipantes()-1);
            }elseif ($usuario->getRol() == "Voluntario"){
                $proyecto->setTotalVoluntarios($proyecto->getTotalVoluntarios()-1);
            }elseif ($usuario->getRol() == "Empleado"){
                $sueldo=$usuario->getSueldoEmpleado();
                $proyecto->setTotalGasto($proyecto->getTotalGasto()-$sueldo);
                $proyecto->setPersonalVinculado($proyecto->getPersonalVinculado()-1);
            }
            $em->flush();
            return new JsonResponse(['nombre' =>$usuario->getNombre()]);
        }else{
            throw new \Exception('No se ha podido eliminar el usuario del proyecto');
        }
    }

    /**
     * @Route("/eliminar_gasto_proyecto", options={"expose"=true}, methods={"POST"}, name="eliminar_gasto_proyecto")
     */
    public function eliminarGastoDelProyecto(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, GastoRepository $gastos, ProyectosRepository $proyectos){
        if ($request->isXmlHttpRequest()){
            $idGasto=$request->get('idGasto');
            $idProyecto=$request->get('idProyecto');
            $em= $doctrine->getManager();
            $gasto=$gastos->find(array('id' => $idGasto));
            $proyecto=$proyectos->find(array('id' => $idProyecto));
            $importeGasto=$gasto->getImporte();
            $gastoTotalProyecto=$proyecto->getTotalGasto();
            $proyecto->setTotalGasto($gastoTotalProyecto-$importeGasto);
            $proyecto->removeGasto($gasto);
            $gastos->remove($gasto);
            $em->flush();
            return new JsonResponse(['nombre' =>$gasto->getDescripcion()]);
        }else{
            throw new \Exception('No se ha podido eliminar el gasto del proyecto');
        }
    }
    /**
     * @Route("/eliminar_ingreso_proyecto", options={"expose"=true}, methods={"POST"}, name="eliminar_ingreso_proyecto")
     */
    public function eliminarIngresoDelProyecto(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, IngresoRepository $ingresos, ProyectosRepository $proyectos){
        if ($request->isXmlHttpRequest()){
            $idIngreso=$request->get('idIngreso');
            $idProyecto=$request->get('idProyecto');
            $em= $doctrine->getManager();
            $ingreso=$ingresos->find(array('id' => $idIngreso));
            $proyecto=$proyectos->find(array('id' => $idProyecto));
            $importeIngreso=$ingreso->getCantidad();
            $ingresoTotalProyecto=$proyecto->getIngresosTotales();
            $proyecto->setIngresosTotales($ingresoTotalProyecto-$importeIngreso);
            $proyecto->removeIngreso($ingreso);
            $ingresos->remove($ingreso);
            $em->flush();
            return new JsonResponse(['nombre' =>$ingreso->getCantidad()]);
        }else{
            throw new \Exception('No se ha podido eliminar el ingreso del proyecto');
        }
    }

    //A continuación estarán las funciones de los charts ->

    private function estadisticasGenero(ChartBuilderInterface $chartBuilder, $masculino, $femenino){
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'labels' => ['Masculino ('.$masculino.'%)', 'Femenino('.$femenino.'%)'],
            'datasets' => [
                [
                    'label' => '% de géneros',
                    'hoverBackgroundColor' => ['skyblue','#ffa8d9'],
                    'borderAlign' => 'inner',
                    'backgroundColor' => ['dodgerblue',
                        '#ff69b4'],
                    'data' => [$masculino, $femenino],
                    'hoverOffset'=> 4,
                    'height' => '25',
                ],
            ],
        ]);


        return $chart;
    }
    private function porcentajeEdad(ChartBuilderInterface $chartBuilder, $usuarios){
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $chart->setData([
            'labels' => ['0 a 20 años ('.round(self::obtenerPorcentajeEdad($usuarios, 0, 20),2).'%)',
                '21 a 30 años ('.round(self::obtenerPorcentajeEdad($usuarios, 21, 30),2).'%)',
                '31 a 40 años ('.round(self::obtenerPorcentajeEdad($usuarios, 31, 40),2).'%)',
                '41 a 50 años ('.round(self::obtenerPorcentajeEdad($usuarios, 41, 50),2).'%)'
                ,'51 a 60 años ('.round(self::obtenerPorcentajeEdad($usuarios, 51, 60),2).'%)',
                '61 a 70 años ('.round(self::obtenerPorcentajeEdad($usuarios, 61, 70),2).'%)',
                '71 a 80 años ('.round(self::obtenerPorcentajeEdad($usuarios, 71, 80),2).'%)',
                '+81 años ('.round(self::obtenerPorcentajeEdad($usuarios, 81, 1000),2).'%)'],
            'datasets' => [
                [
                    'hoverOffset'=> 4,
                    'height' => '25',
                    'label' => '% de edades',
                    'borderWidth' => 1,
                    'borderAlign' => 'inner',
                    'backgroundColor' => ['rgb(210, 45, 45)',
                        'rgb(0, 191, 255)',
                        'rgb(255, 153, 153)',
                        'rgb(255, 255, 0)',
                        'rgb(191, 0, 255)',
                        'rgb(0, 255, 255)',
                        'rgb(128, 255, 0)',
                        'rgb(255, 230, 230)'],

                    'data' => [round(self::obtenerPorcentajeEdad($usuarios, 0, 20),2),
        round(self::obtenerPorcentajeEdad($usuarios, 21, 30),2),
        round(self::obtenerPorcentajeEdad($usuarios, 31, 40),2),
        round(self::obtenerPorcentajeEdad($usuarios, 41, 50),2),
        round(self::obtenerPorcentajeEdad($usuarios, 51, 60),2),
        round(self::obtenerPorcentajeEdad($usuarios, 61, 70),2),
        round(self::obtenerPorcentajeEdad($usuarios, 71, 80),2),
        round(self::obtenerPorcentajeEdad($usuarios, 81, 1000), 2)],
                ],
            ],
        ]);


        return $chart;
    }
    private function porcentajeNacionalidades(ChartBuilderInterface $chartBuilder, $usuarios){

        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $paises=self::porcentajePaisesChart($usuarios);
        $p=[];
        $x=[];


        $i=0;
        foreach($paises as $key => $value)
        {
            $p[$i] = $key." (".$value."%)";
            $x[$i] = $value;
            $i++;
        }
        $labels=$p;

        $chart->setData([
            'labels' => $labels,

            'datasets' => [
                [
                    'hoverOffset'=> 4,
                    'height' => '25',
                    'label' => '% de edades',
                    'borderWidth' => 1,
                    'borderAlign' => 'inner',
                    'backgroundColor' => ['rgb(255, 0, 0)',
                        'rgb(128, 0, 128)',
                        'rgb(65, 105, 225)',
                        'rgb(160, 82, 45)',
                        'rgb(255, 245, 238)',
                        'rgb(0, 255, 127)',
                        'rgb(255, 255, 0)',
                        'rgb(0, 0, 128)',
                        'rgb(255, 0, 255)',
                        'rgb(255, 248, 220)'],

                    'data' => $x,
                ],
            ],
        ]);


        return $chart;
    }
    private function estadisticasEstadoCivil(ChartBuilderInterface $chartBuilder, $usuarios){
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'labels' => ['Solteros ('.self::obtenerPorcentajeEstadoCivil($usuarios, "Soltero").'%)', 'Casados('.self::obtenerPorcentajeEstadoCivil($usuarios, "Casado").'%)'],
            'datasets' => [
                [
                    'label' => '% de géneros',
                    'hoverBackgroundColor' => ['#33ff5e','#33acff'],
                    'borderAlign' => 'inner',
                    'backgroundColor' => ['#36ff33',
                        '#33d1ff'],
                    'data' => [self::obtenerPorcentajeEstadoCivil($usuarios, "Soltero"),
                        self::obtenerPorcentajeEstadoCivil($usuarios, "Casado")],
                    'hoverOffset'=> 4,
                    'height' => '25',
                ],
            ],
        ]);


        return $chart;
    }

    private function estadisticasPapelesEnRegla(ChartBuilderInterface $chartBuilder, $usuarios){
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'labels' => ['Papeles en regla ('.self::obtenerPorcentajeDocumentacion($usuarios, "Si").'%)', 'Papeles sin arreglar ('.self::obtenerPorcentajeDocumentacion($usuarios, "No").'%)'],
            'datasets' => [
                [
                    'borderAlign' => 'inner',
                    'backgroundColor' => ['rgb(127, 255, 0)','rgb(220, 20, 60)'],
                    'data' => [self::obtenerPorcentajeDocumentacion($usuarios, "Si"), self::obtenerPorcentajeDocumentacion($usuarios, "No")],
                    'hoverOffset'=> 4,
                    'height' => '25',
                ],
            ],
        ]);


        return $chart;
    }

    private function estadisticasDiscapacidad(ChartBuilderInterface $chartBuilder, $usuarios){
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'labels' => ['Usuarios con discapacidad ('.self::obtenerPorcentajeDiscapacidad($usuarios, "Si").'%)', 'Usuarios sin discapacidad ('.self::obtenerPorcentajeDiscapacidad($usuarios, "No").'%)'],
            'datasets' => [
                [
                    'borderAlign' => 'inner',
                    'backgroundColor' => ['rgb(184, 134, 11)','rgb(0, 206, 209)'],
                    'data' => [self::obtenerPorcentajeDiscapacidad($usuarios, "Si"), self::obtenerPorcentajeDiscapacidad($usuarios, "No")],
                    'hoverOffset'=> 4,
                    'height' => '25',
                ],
            ],
        ]);


        return $chart;
    }

    private function estadisticasGastos(ChartBuilderInterface $chartBuilder, $gastos, $gastoTotal, $gastoEmpleados){
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $gast=self::obtenerEstadísticasGastos($gastos, $gastoTotal,$gastoEmpleados);
        $p=[];
        $x=[];


        $i=0;
        foreach($gast as $key => $value)
        {
            $p[$i] = $key." (".$value."%)";
            $x[$i] = $value;
            $i++;
        }
        $labels=$p;
        $colores=["rgb(0, 0, 255)","rgb(165, 42, 42)","rgb(127, 255, 0)","rgb(255, 127, 80)","rgb(184, 134, 11)","rgb(0, 100, 0)","rgb(169, 169, 169)","rgb(139, 0, 0)","rgb(255, 20, 147)","rgb(255, 215, 0)",
            "rgb(240, 255, 240)","rgb(230, 230, 250)","rgb(75, 0, 130)","rgb(124, 252, 0)","rgb(255, 0, 255)","rgb(128, 0, 0)","rgb(0, 0, 205)","rgb(255, 228, 181)","rgb(255, 69, 0)","rgb(255, 165, 0)","rgb(0, 0, 255)","rgb(165, 42, 42)","rgb(127, 255, 0)","rgb(255, 127, 80)","rgb(184, 134, 11)","rgb(0, 100, 0)","rgb(169, 169, 169)","rgb(139, 0, 0)","rgb(255, 20, 147)","rgb(255, 215, 0)",
            "rgb(240, 255, 240)","rgb(230, 230, 250)","rgb(75, 0, 130)","rgb(124, 252, 0)","rgb(255, 0, 255)","rgb(128, 0, 0)","rgb(0, 0, 205)","rgb(255, 228, 181)","rgb(255, 69, 0)","rgb(255, 165, 0)"];
        $chart->setData([
            'labels' => $labels,

            'datasets' => [
                [
                    'hoverOffset'=> 2,
                    'label' => 'Gastos',
                    'borderAlign' => 'inner',
                    'backgroundColor' => $colores,
                    'borderColor' => $colores,

                    'data' => $x,
                ],
            ],

        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);


        return $chart;
    }
    private function estadisticasIngresos(ChartBuilderInterface $chartBuilder, $ingresos, $totalIngresos){
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $gast=self::obtenerEstadisticasIngresos($ingresos, $totalIngresos);
        $p=[];
        $x=[];


        $i=0;
        foreach($gast as $key => $value)
        {
            $p[$i] = $key." (".$value."%)";
            $x[$i] = $value;
            $i++;
        }
        $labels=$p;
        $colores=["rgb(0, 0, 255)","rgb(165, 42, 42)","rgb(127, 255, 0)","rgb(255, 127, 80)","rgb(184, 134, 11)","rgb(0, 100, 0)","rgb(169, 169, 169)","rgb(139, 0, 0)","rgb(255, 20, 147)","rgb(255, 215, 0)",
            "rgb(240, 255, 240)","rgb(230, 230, 250)","rgb(75, 0, 130)","rgb(124, 252, 0)","rgb(255, 0, 255)","rgb(128, 0, 0)","rgb(0, 0, 205)","rgb(255, 228, 181)","rgb(255, 69, 0)","rgb(255, 165, 0)","rgb(0, 0, 255)","rgb(165, 42, 42)","rgb(127, 255, 0)","rgb(255, 127, 80)","rgb(184, 134, 11)","rgb(0, 100, 0)","rgb(169, 169, 169)","rgb(139, 0, 0)","rgb(255, 20, 147)","rgb(255, 215, 0)",
            "rgb(240, 255, 240)","rgb(230, 230, 250)","rgb(75, 0, 130)","rgb(124, 252, 0)","rgb(255, 0, 255)","rgb(128, 0, 0)","rgb(0, 0, 205)","rgb(255, 228, 181)","rgb(255, 69, 0)","rgb(255, 165, 0)"];
        $chart->setData([
            'labels' => $labels,

            'datasets' => [
                [
                    'hoverOffset'=> 2,
                    'label' => 'Ingresos',
                    'borderAlign' => 'inner',
                    'backgroundColor' => $colores,
                    'borderColor' => $colores,

                    'data' => $x,
                ],
            ],

        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);


        return $chart;
    }
    //-------------------------------------------------FIN CHARTS---------------------->

    //-------------------FUNCIONES AUXILIARES PARA OBTENER PORCENTAJES Y CONTADORES ----------------------------->
    private function obtenerPorcentajeSexo($usuarios, $sexo){
        $totalUsuarios=sizeof($usuarios);
        $femenino=0;
        $masculino=0;
        foreach($usuarios as $user){
            if($user['sexo'] == "Masculino"){
                $masculino += 1;
            }else{
                $femenino += 1;
            }
        }
        if($sexo == "Masculino"){
            return round((($masculino/$totalUsuarios)*100),2);
        }else{
            return round((($femenino/$totalUsuarios)*100),2);
        }
    }
    private function obtenerPorcentajeEdad($usuarios, $firstAge, $secondAge){
        $totalUsuarios=sizeof($usuarios);
        $aux=0;
        foreach($usuarios as $user){
            $edad=self::obtener_edad_segun_fecha($user['fecha_nacimiento']);

            if($edad >= $firstAge and $edad<=$secondAge){
                $aux+=1;
            }
        }
        return (($aux/$totalUsuarios)*100);
    }
    private function obtenerTotalEdades($usuarios, $firstAge, $secondAge){
        $totalUsuarios=sizeof($usuarios);
        $aux=0;
        foreach($usuarios as $user){
            $edad=self::obtener_edad_segun_fecha($user['fecha_nacimiento']);

            if($edad >= $firstAge and $edad<=$secondAge){
                $aux+=1;
            }
        }
        return $aux;
    }
    function obtener_edad_segun_fecha($fechaNacimiento)
    {

        $ahora = new DateTime(date("Y-m-d"));
        $diferencia = $ahora->diff(new DateTime($fechaNacimiento));
        return $diferencia->format("%y");
    }

    private function obtenerPorcentajeEstadoCivil($usuarios, $estadoCivil){
        $totalUsuarios=sizeof($usuarios);
        $soltero=0;
        $casado=0;
        foreach($usuarios as $user){
            if($user['estado_civil'] == "Soltero"){
                $soltero += 1;
            }else{
                $casado += 1;
            }
        }
        if($estadoCivil == "Soltero"){
            return round((($soltero/$totalUsuarios)*100),2);
        }else{
            return round((($casado/$totalUsuarios)*100),2);
        }
    }
    private function obtenerPorcentajeDiscapacidad($usuarios, $discapacidad){
        $totalUsuarios=sizeof($usuarios);
        $si=0;
        $no=0;
        foreach($usuarios as $user){
            if($user['discapacidad'] == "Si"){
                $si += 1;
            }else{
                $no += 1;
            }
        }
        if($discapacidad == "Si"){
            return round((($si/$totalUsuarios)*100),2);
        }else{
            return round((($no/$totalUsuarios)*100),2);
        }
    }
    private function obtenerPorcentajeDocumentacion($usuarios, $documentacion){
        $totalUsuarios=sizeof($usuarios);
        $si=0;
        $no=0;
        foreach($usuarios as $user){
            if($user['documentacion_legal'] == "Si"){
                $si += 1;
            }else{
                $no += 1;
            }
        }
        if($documentacion == "Si"){
            return round((($si/$totalUsuarios)*100),2);
        }else{
            return round((($no/$totalUsuarios)*100),2);
        }
    }
    private function obtenerTotal($usuarios, $campo, $valorCampo){
        $numero=0;
        foreach($usuarios as $user){
            if($user[$campo] == $valorCampo) {
                $numero += 1;
            }

        }
            return $numero;

    }
    private function principalesPaises($usuarios){
        $p= [];
        foreach($usuarios as $user){
            if(sizeof($p)==0){
                $valor=1;
                $porcentaje=self::obtenerPorcentajePaises($usuarios, $user['nacionalidad']);
                $p[$user['nacionalidad']] = $porcentaje."% - ".$valor;
            }else {
                if(array_key_exists($user['nacionalidad'], $p)){
                    $valor=self::contar($user['nacionalidad'], $usuarios);
                    $porcentaje=self::obtenerPorcentajePaises($usuarios, $user['nacionalidad']);

                    $p[$user['nacionalidad']] = $porcentaje."% - ".$valor;

                }else{
                    $valor=1;
                    $porcentaje=self::obtenerPorcentajePaises($usuarios, $user['nacionalidad']);
                    $p[$user['nacionalidad']] = $porcentaje."% - ".$valor;

                }
            }
        }
        arsort($p);
        return $p;

    }
    private function porcentajePaisesChart($usuarios){
        $p= [];
        foreach($usuarios as $user){
            if(sizeof($p)==0){
                $porcentaje=self::obtenerPorcentajePaises($usuarios, $user['nacionalidad']);
                $p[$user['nacionalidad']] = $porcentaje;
            }else {
                if(array_key_exists($user['nacionalidad'], $p)){
                    $porcentaje=self::obtenerPorcentajePaises($usuarios, $user['nacionalidad']);

                    $p[$user['nacionalidad']] = $porcentaje;

                }else{
                    $porcentaje=self::obtenerPorcentajePaises($usuarios, $user['nacionalidad']);
                    $p[$user['nacionalidad']] = $porcentaje;

                }
            }
        }
        arsort($p);
        $p = array_slice($p, 0, 10);
        return $p;

    }
    private function obtenerPorcentajePaises($usuarios, $pais){
        $totalUsuarios=sizeof($usuarios);
        $cont=0;
        foreach($usuarios as $user){
            if($user['nacionalidad'] == $pais){
                $cont=$cont+1;
            }
        }
        return round((($cont/$totalUsuarios)*100),2);
    }
    private function contar($pais, $usuarios){
        $cont=0;
        foreach($usuarios as $user){
            if($user['nacionalidad'] == $pais){
                $cont=$cont+1;
            }
        }
        return $cont;
    }
    private function obtenerGastos($gastos, $gastoTotal, $gastoEmpleados){
        $p=[];

        $p['Empleados']=$gastoEmpleados." € - ".round(self::obtenerPorcentaje($gastoEmpleados, $gastoTotal),2)."%";
        foreach($gastos as $gasto){
            $nombreCategoria=$gasto->getCategoria()->getNombre();

            if(array_key_exists($nombreCategoria, $p)){
                $importe=self::contarGastos($gastos, $nombreCategoria);
                $porcentaje=round(self::obtenerPorcentaje($importe, $gastoTotal),2);
                $p[$nombreCategoria]=$importe." € - ".$porcentaje."%";
            }else{
                $importe=$gasto->getImporte();
                $porcentaje=round(self::obtenerPorcentaje($importe, $gastoTotal),2);
                $p[$nombreCategoria]=$importe." € - ".$porcentaje."%";
            }

        }
        return $p;
    }
    private function obtenerEstadísticasGastos($gastos, $gastoTotal, $gastoEmpleados){
        $p=[];

        $p['Empleados']=round(self::obtenerPorcentaje($gastoEmpleados, $gastoTotal),2);
        foreach($gastos as $gasto){
            $nombreCategoria=$gasto->getCategoria()->getNombre();

            if(array_key_exists($nombreCategoria, $p)){
                $importe=self::contarGastos($gastos, $nombreCategoria);
                $porcentaje=round(self::obtenerPorcentaje($importe, $gastoTotal),2);
                $p[$nombreCategoria]=$porcentaje;
            }else{
                $importe=$gasto->getImporte();
                $porcentaje=round(self::obtenerPorcentaje($importe, $gastoTotal),2);
                $p[$nombreCategoria]=$porcentaje;
            }

        }
        return $p;
    }
    private function contarGastos($gastos, $nombreCategoria){
        $total=0;
        foreach($gastos as $gasto){
            if($gasto->getCategoria()->getNombre() == $nombreCategoria){
                $total=$total+$gasto->getImporte();
            }
        }
        return $total;
    }
    private function obtenerPorcentaje($importe, $total){
        return round((($importe/$total)*100),2);
    }

    private function obtenerPorcentajeIngresos($ingresos, $totalIngresos){
        $p=[];
        $tipo="";
        foreach($ingresos as $ingreso){
            if($ingreso->getTipo()==1){
                $tipo="Administración pública";
            }elseif($ingreso->getTipo()==2){
                $tipo="Entidad privada";
            }elseif($ingreso->getTipo()==3){
                $tipo="Fondos propios";
            }
            if(array_key_exists($tipo, $p)){
                $cantidad=self::contarIngresos($ingresos, $ingreso->getTipo());
                $porcentaje=self::obtenerPorcentaje($cantidad, $totalIngresos);
                $p[$tipo]=$cantidad." € - ".$porcentaje;
            }else{
                $porcentaje=self::obtenerPorcentaje($ingreso->getCantidad(), $totalIngresos);
                $p[$tipo]=$ingreso->getCantidad()." € - ".$porcentaje;
            }

        }
        return $p;
    }
    private function obtenerEstadisticasIngresos($ingresos, $totalIngresos){
        $p=[];
        $tipo="";
        foreach($ingresos as $ingreso){
            if($ingreso->getTipo()==1){
                $tipo="Administración pública";
            }elseif($ingreso->getTipo()==2){
                $tipo="Entidad privada";
            }elseif($ingreso->getTipo()==3){
                $tipo="Fondos propios";
            }
            if(array_key_exists($tipo, $p)){
                $cantidad=self::contarIngresos($ingresos, $ingreso->getTipo());
                $porcentaje=self::obtenerPorcentaje($cantidad, $totalIngresos);
                $p[$tipo]=$porcentaje;
            }else{
                $porcentaje=self::obtenerPorcentaje($ingreso->getCantidad(), $totalIngresos);
                $p[$tipo]=$porcentaje;
            }

        }
        return $p;
    }
    private function contarIngresos($ingresos, $tipo){
        $total=0;
        foreach($ingresos as $ingreso){
            if($ingreso->getTipo() == $tipo){
                $total=$total+$ingreso->getCantidad();
            }
        }
        return $total;
    }
    //----------------------FIN FUNCIONES AUXILIARES--------------------------------------------------------------------->


    //---------------FUNCIÓN PARA EXPORTAR EL INFORME A PDF------------------------------------------------>
    #[Route('/pdfExport/{idProyecto}', name: 'exportar-pdf')]
    public function toPdf($idProyecto, ProyectosRepository $proyectos, UserRepository $users, ChartBuilderInterface $chartBuilder){
        $proyecto = $proyectos ->find($idProyecto);
        $usuarios=$users->verUsuariosDelProyecto($idProyecto);
        $voluntarios=$users->verVoluntariosDelProyecto($idProyecto);
        $empleados=$users->verEmpleadosDelProyecto($idProyecto);
        $adminSede=$proyecto->getSede()->getAdministradorSede()->getId();
        $gastos=$proyecto->getGastos();
        $gastoTotal=0;
        foreach($gastos as $gasto){
            $gastoTotal += $gasto->getImporte();
        }
        $gastoEmpleados=0;
        foreach($empleados as $empleado){
            $gastoEmpleados=$gastoEmpleados+$empleado['sueldo_empleado'];
        }
        $gastoTotal=$gastoTotal+$gastoEmpleados;
        $ingresos=$proyecto->getIngresos();
        $ingresosTotales=0;
        foreach($ingresos as $ingreso){
            $ingresosTotales += $ingreso->getCantidad();
        }
        if(sizeof($gastos) > 0) {
            $p=self::obtenerGastos($gastos, $gastoTotal,$gastoEmpleados);
        }else{
            $p=null;
        }
        if(sizeof($ingresos) > 0){
            $ingresosEstadisticas=self::obtenerPorcentajeIngresos($ingresos, $ingresosTotales);
        }else{
            $ingresosEstadisticas=null;
        }

        //estadísticas ->
        if(!(empty($usuarios))) {
            $pHombres = round(self::obtenerPorcentajeSexo($usuarios, "Masculino"), 2);
            $pMujeres = round(self::obtenerPorcentajeSexo($usuarios, "Femenino"), 2);
            $totalHombres = self::obtenerTotal($usuarios, "sexo", "Masculino");
            $totalMujeres = self::obtenerTotal($usuarios, "sexo", "Femenino");

            $pSolteros=round(self::obtenerPorcentajeEstadoCivil($usuarios, "Soltero"),2);
            $pCasados=round(self::obtenerPorcentajeEstadoCivil($usuarios, "Casado"),2);
            $totalSolteros=self::obtenerTotal($usuarios, "estado_civil","Soltero");
            $totalCasados=self::obtenerTotal($usuarios, "estado_civil","Casado");

            $pDiscapacidad=round(self::obtenerPorcentajeDiscapacidad($usuarios, "Si"),2);
            $pNoDiscapacidad=round(self::obtenerPorcentajeDiscapacidad($usuarios, "No"),2);
            $totalDiscapacidad=self::obtenerTotal($usuarios, "discapacidad","Si");
            $totalNoDiscapacidad=self::obtenerTotal($usuarios, "discapacidad","No");

            $pDocumentacionLegal=round(self::obtenerPorcentajeDocumentacion($usuarios, "Si"), 2);
            $pNoDocumentacionLegal=round(self::obtenerPorcentajeDocumentacion($usuarios, "No"), 2);
            $totalDocumentacionLegal=self::obtenerTotal($usuarios, "documentacion_legal","Si");
            $totalNoDocumentacionLegal=self::obtenerTotal($usuarios, "documentacion_legal","No");

            $firstAge=round(self::obtenerPorcentajeEdad($usuarios, 0, 20),2);
            $secondAge=round(self::obtenerPorcentajeEdad($usuarios, 21, 30),2);
            $thirdAge=round(self::obtenerPorcentajeEdad($usuarios, 31, 40),2);
            $fourthAge=round(self::obtenerPorcentajeEdad($usuarios, 41, 50),2);
            $fifthAge=round(self::obtenerPorcentajeEdad($usuarios, 51, 60),2);
            $sixthAge=round(self::obtenerPorcentajeEdad($usuarios, 61, 70),2);
            $seventhAge=round(self::obtenerPorcentajeEdad($usuarios, 71, 80),2);
            $eightAge=round(self::obtenerPorcentajeEdad($usuarios, 81, 1000), 2);

            $tfirstAge=round(self::obtenerTotalEdades($usuarios, 0, 20));
            $tsecondAge=round(self::obtenerTotalEdades($usuarios, 21, 30));
            $tthirdAge=round(self::obtenerTotalEdades($usuarios, 31, 40));
            $tfourthAge=round(self::obtenerTotalEdades($usuarios, 41, 50));
            $tfifthAge=round(self::obtenerTotalEdades($usuarios, 51, 60));
            $tsixthAge=round(self::obtenerTotalEdades($usuarios, 61, 70));
            $tseventhAge=round(self::obtenerTotalEdades($usuarios, 71, 80));
            $teightAge=round(self::obtenerTotalEdades($usuarios, 81, 1000));

            $paises=self::principalesPaises($usuarios);



        }else{
            $pHombres = null;
            $pMujeres = null;
            $totalHombres = null;
            $totalMujeres = null;
        }

        $nombreFichero=$proyecto->getNombre().".pdf";
        $mpdf = new Mpdf(['mode' => 'utf-8']);
        $mpdf->WriteHTML($this->renderView('pdf-resumen-tables.html.twig',[
            'proyecto' => $proyecto, 'usuarios'=>$usuarios, 'voluntarios' => $voluntarios, 'gastos' => $gastos, 'gastoTotal'=>$gastoTotal, 'empleados' => $empleados, 'ingresos'=>$ingresos, 'ingresoTotal'=>$ingresosTotales,
            'totalUsuarios' =>sizeof($usuarios)
        ]));
        $mpdf->AddPage();
        $mpdf->WriteHTML($this->renderView('pdf-tables-gastos-ingresos.html.twig',[
            'proyecto' => $proyecto, 'usuarios'=>$usuarios, 'voluntarios' => $voluntarios, 'gastos' => $gastos, 'gastoTotal'=>$gastoTotal, 'empleados' => $empleados, 'ingresos'=>$ingresos, 'ingresoTotal'=>$ingresosTotales,
            'totalUsuarios' =>sizeof($usuarios)
        ]));

        $mpdf->AddPage();

        $mpdf->WriteHTML($this->renderView('pdf-estadisticas-usuarios.twig',[
            'Phombres'=>$pHombres, 'Pmujeres' => $pMujeres,'totalHombres'=>$totalHombres,'totalMujeres'=>$totalMujeres,
            'pSolteros'=>$pSolteros,'pCasados'=>$pCasados,'totalSolteros'=>$totalSolteros,'totalCasados'=>$totalCasados,
            'pDiscapacidad'=>$pDiscapacidad,'pNoDiscapacidad'=>$pNoDiscapacidad,'totalDiscapacidad'=>$totalDiscapacidad,'totalNoDiscapacidad'=>$totalNoDiscapacidad,
            'pDocumentacionLegal'=>$pDocumentacionLegal,'pNoDocumentacionLegal'=>$pNoDocumentacionLegal,'totalDocumentacionLegal'=>$totalDocumentacionLegal,'totalNoDocumentacionLegal'=>$totalNoDocumentacionLegal,
            'one'=>$firstAge,'two'=>$secondAge,'three'=>$thirdAge,'four'=>$fourthAge,'five'=>$fifthAge,'six'=>$sixthAge,'seven'=>$seventhAge,'eight'=>$eightAge,
            'tone'=>$tfirstAge,'ttwo'=>$tsecondAge,'tthree'=>$tthirdAge,'tfour'=>$tfourthAge,'tfive'=>$tfifthAge,'tsix'=>$tsixthAge,'tseven'=>$tseventhAge,'teight'=>$teightAge,
            'paises'=>$paises,

            'totalUsuarios' =>sizeof($usuarios)
        ]));
        $mpdf->AddPage();
        $mpdf->WriteHTML($this->renderView('pdf-estadisticas-gastos.html.twig',[
            'gastos'=>$p,'gastoTotal'=>$gastoTotal,'ingresos'=>$ingresosEstadisticas,'ingresoTotal'=>$ingresosTotales,
            'totalUsuarios' =>sizeof($usuarios)
        ]));

        $mpdf->Output($nombreFichero, 'I');
        return new Response();
    }
    //---------------------FIN EXPORTAR PDF------------------------------


}
