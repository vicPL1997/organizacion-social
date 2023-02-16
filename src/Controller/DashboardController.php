<?php

namespace App\Controller;

use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProyectosRepository;
use App\Repository\SedesRepository;
use Doctrine;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ChartBuilderInterface $chartBuilder,SedesRepository $sedes, UserRepository $users, ProyectosRepository $proyectos): Response
    {

        $ahora = new \DateTime((date('d-m-Y H:i:s')));

        $allSedes = $sedes->findAll();
        $allUsers = $users->findAll();

        $allProyectos = $proyectos->findAll();
        $totalSedes = sizeof($allSedes);
        $totalProyectos = sizeof($allProyectos);
        $totalUsuarios = sizeof($allUsers);


        return $this->render('dashboard/index.html.twig', ['sedes' => $allSedes, 'ahora'=> $ahora, 'usuarios' => $allUsers, 'proyectos' => $allProyectos,
            'totalSedes' => $totalSedes, 'totalProyectos' => $totalProyectos, 'totalUsuarios' => $totalUsuarios]);

    }
    #[Route('/dashboardSedes', name: 'dashboard_sedes')]
    public function dashboardSedes(SedesRepository $sedes, UserRepository $users, ProyectosRepository $proyectos): Response
    {
        $allSedes = $sedes->findAll();
        $allUsers = $users->findAll();
        $allProyectos = $proyectos->findAll();
        $totalSedes = sizeof($allSedes);


        return $this->render('dashboard/sedes.html.twig', ['sedes' => $allSedes,  'totalSedes' => $totalSedes]);

    }
    /**
     * @IsGranted("ROLE_ADMIN_SEDES")
     */
    #[Route('/dashboardMiSede', name: 'dashboard_Misede')]
    public function dashboardMiSede(SedesRepository $sedes, UserRepository $users, ProyectosRepository $proyectos): Response
    {
        $tieneSede=true;
        $user=$this->getUser();
        $idUser=$user->getId();
        $sede=$sedes->findOneBy(array('administradorSede' => $idUser));
        if(is_null($sede)){
            $tieneSede=false;
            $proyectos=null;
            $totalProyectos=null;
        }else{
            $proyectos=$sede->getProyectos();
            $totalProyectos=sizeof($proyectos);
        }


        return $this->render('dashboard/miSede.html.twig', ['ac' => $sede, 'totalProyectos' => $totalProyectos, 'proyectos' => $proyectos, 'tieneSede'=>$tieneSede]);

    }

    #[Route('/dashboardProyectos', name: 'dashboard_proyectos')]
    public function dashboardProyectos(SedesRepository $sedes, UserRepository $users, ProyectosRepository $proyectos): Response
    {
        $allSedes = $sedes->findAll();
        $allUsers = $users->findAll();
        $allProyectos = $proyectos->findAll();
        $totalProyectos = sizeof($allProyectos);


        return $this->render('dashboard/proyectos.html.twig', ['proyectos' => $allProyectos,  'totalProyectos' => $totalProyectos]);

    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_SEDES')")
     */
    #[Route('/dashboardTodosUsuarios', name: 'dashboard_todosUsuarios')]
    public function dashboardUsuarios(SedesRepository $sedes, UserRepository $users, ProyectosRepository $proyectos): Response
    {
        $allUsers = $users->findAll();
        $usuarios= sizeof($users->findBy(array('rol' => 'Usuario')));
        $voluntarios= sizeof($users->findBy(array('rol' => 'Voluntario')));
        $adSedes= sizeof($users->findBy(array('rol' => 'Administrador de sede')));

        return $this->render('dashboard/dashboardUsuarios.html.twig', ['usuarios' => $allUsers, 'nUsuarios'=>$usuarios, 'nVoluntarios'=>$voluntarios, 'nSedes'=>$adSedes]);

    }
    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_SEDES')")
     */
    #[Route('/dashboardTodosUsuariosAdminSedes', name: 'dashboard_todosUsuariosAdminSedes')]
    public function dashboardUsuariosAdminSedes(SedesRepository $sedes, UserRepository $users, ProyectosRepository $proyectos): Response
    {
        //$allUsers = $users->findAll();
        $usuarios= $users->findBy(array('rol' => 'Administrador de sede'));


        return $this->render('dashboard/dashboardUsuariosAdminSedes.html.twig', ['usuarios' => $usuarios]);

    }
    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_SEDES')")
     */
    #[Route('/dashboardTodosUsuariosVoluntarios', name: 'dashboard_todosUsuariosVoluntarios')]
    public function dashboardUsuariosVoluntarios(SedesRepository $sedes, UserRepository $users, ProyectosRepository $proyectos): Response
    {
        //$allUsers = $users->findAll();
        $usuarios= $users->findBy(array('rol' => 'Voluntario'));


        return $this->render('dashboard/dashboardUsuariosVoluntarios.html.twig', ['usuarios' => $usuarios]);

    }
    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_ADMIN_SEDES')")
     */
    #[Route('/dashboardTodosUsuariosParticipantes', name: 'dashboard_todosUsuariosParticipantes')]
    public function dashboardUsuariosParticipantes( UserRepository $users): Response
    {
        //$allUsers = $users->findAll();
        $usuarios= $users->findBy(array('rol' => 'Usuario'));


        return $this->render('dashboard/dashboardUsuariosParticipantes.html.twig', ['usuarios' => $usuarios]);

    }
    /**
     * @Route("/eliminar_sede", options={"expose"=true}, methods={"POST"}, name="eliminar_sede")
     */
    public function eliminarSede(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, SedesRepository $sedes, ProyectosRepository $proyectos){
        if ($request->isXmlHttpRequest()){
            $id=$request->get('id');
            $em= $doctrine->getManager();
            $sede=$sedes->find(array('id' => $id));
            $adminSede=$sede->getAdministradorSede();
            if(!(is_null($adminSede))){
                $adminSede->setTieneSedeAdministrada(0);
            }
            $proyectosSede=$sede->getProyectos();
            foreach($proyectosSede as $proyecto){
                $users=$proyecto->getUsers();
                foreach($users as $user){
                    $proyecto->removeUser($user);
                }
                $gastos=$proyecto->getGastos();
                foreach($gastos as $gasto){
                    $proyecto->removeGasto($gasto);
                }
                $ingresos=$proyecto->getIngresos();
                foreach($ingresos as $ingreso){
                    $proyecto->removeIngreso($ingreso);
                }
                $proyectos->remove($proyecto);
            }
            $nombre=$sede->getNombre();
            $sedes->remove($sede);
            $em->flush();
            return new JsonResponse(['nombre' =>$nombre]);
        }else{
            throw new \Exception('No se ha podido eliminar la sede');
        }
    }
    /**
     * @Route("/eliminar_proyecto", options={"expose"=true}, methods={"POST"}, name="eliminar_proyecto")
     */
    public function eliminarProyecto(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine,  ProyectosRepository $proyectos){
        if ($request->isXmlHttpRequest()){
            $id=$request->get('id');
            $em= $doctrine->getManager();
            $proyecto=$proyectos->find(array('id' => $id));
            $sede=$proyecto->getSede();
            $users=$proyecto->getUsers();
            foreach($users as $user){
                $proyecto->removeUser($user);
            }
            $gastos=$proyecto->getGastos();
            foreach($gastos as $gasto){
                $proyecto->removeGasto($gasto);
            }
            $ingresos=$proyecto->getIngresos();
            foreach($ingresos as $ingreso){
                $proyecto->removeIngreso($ingreso);
            }
            $sede->removeProyecto($proyecto);
            $proyectos->remove($proyecto);

            $em->flush();
            return new JsonResponse(['nombre']);
        }else{
            throw new \Exception('No se ha podido eliminar la sede');
        }
    }
}
