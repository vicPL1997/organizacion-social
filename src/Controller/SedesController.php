<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Sedes;
use App\Entity\User;

use App\Form\PostType;
use App\Form\SedeType;
use App\Repository\ProyectosRepository;
use App\Repository\SedesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SedesController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/crearSedes', name: 'crear_sedes')]
    public function index(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, UserRepository $users, SedesRepository $sedes)
    {
        $sede = new Sedes();
        $form= $this->createForm(SedeType::class, $sede);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em= $doctrine->getManager();
            $em->persist($sede);
            $administradorSede=$sede->getAdministradorSede();
            $administradorSede->setTieneSedeAdministrada(1);

            $em->flush();

            $this->addFlash('exitoSede', 'Se ha creado la sede correctamente');
            return $this->redirectToRoute('dashboard_sedes');
        }         $this->addFlash('falloSede', 'Ya existía dicha sede');
        return $this->render('sedes/index.html.twig', [
            'controller_name' => 'SedesController',
            'form' => $form->createView()

        ]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/editarSede/{id}', name: 'editar_sede')]
    public function editarSede(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, ProyectosRepository $proyectos, SedesRepository $sedes, Sedes $sede)
    {
        $antiguoAdmin=$sede->getAdministradorSede();
        $antiguaComunidad=$sede->getLocalizacion();
        $em= $doctrine->getManager();

        $antiguoAdmin->setTieneSedeAdministrada(0);
        $em->flush();
        $form= $this->createForm(SedeType::class, $sede);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em= $doctrine->getManager();
            $nuevaComunidad=$sede->getLocalizacion();
            if($antiguaComunidad!=$nuevaComunidad){
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
            }
            $administradorSede=$sede->getAdministradorSede();
            $administradorSede->setTieneSedeAdministrada(1);

            $em->flush();

            $this->addFlash('exitoEditSede', 'Se ha editado la sede correctamente');
            return $this->redirectToRoute('dashboard_sedes');
        }else{
            $this->addFlash('falloSede', 'No se ha podido editar la sede');
        }
        return $this->render('sedes/sedeEdit.html.twig', [
            'form' => $form->createView()

        ]);
    }
    #[Route('/sede/{id}', name: 'verSede')]
    public function verSede($id, SedesRepository $sedes){

        $sede = $sedes ->find($id);
        $proyectos = $sede -> getProyectos();
        $totalProyectos=sizeof($proyectos);
        return $this->render('dashboard/verSede.html.twig', [
            'ac' => $sede, 'proyectos' => $proyectos, 'totalProyectos'=>$totalProyectos

        ]);
    }


}