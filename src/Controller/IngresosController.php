<?php

namespace App\Controller;

use App\Entity\Gasto;
use App\Entity\Ingreso;
use App\Form\GastoType;
use App\Form\IngresoType;
use App\Repository\ProyectosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine;

class IngresosController extends AbstractController
{
    #[Route('/Crear-ingreso/{idProyecto}', name: 'crear-ingreso')]
    public function index(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, ProyectosRepository $proyectos, $idProyecto): Response
    {
        $ingreso = new Ingreso();
        $form= $this->createForm(ingresoType::class, $ingreso);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em= $doctrine->getManager();
            $proyecto=$proyectos->findOneBy(array('id' => $idProyecto));
            $ingreso->setProyecto($proyecto);
            $ingresosTotales=$proyecto->getIngresosTotales();
            $ingresoNuevo=$ingresosTotales+($ingreso->getCantidad());
            $proyecto->setIngresosTotales($ingresoNuevo);
            $em->persist($ingreso);
            $em->flush();

            $this->addFlash('exitoIngreso', 'Se ha creado el ingreso correctamente');
            return $this->redirectToRoute('verProyecto', ['id'=>$idProyecto,'tab'=>5]);
        }
        return $this->render('ingresos/index.html.twig', [
            'form' => $form->createView()

        ]);
    }

    #[Route('/editar-ingreso/{id}/{idProyecto}', name: 'editar-ingreso')]
    public function editarIngreso(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, ProyectosRepository $proyectos, $idProyecto, Ingreso $ingreso): Response
    {
        $ingresoAntiguo= $ingreso->getCantidad();
        $form= $this->createForm(ingresoType::class, $ingreso);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em= $doctrine->getManager();
            $proyecto=$proyectos->findOneBy(array('id' => $idProyecto));
            $ingreso->setProyecto($proyecto);
            $ingresosTotales=$proyecto->getIngresosTotales();
            $ingresoNuevo=$ingreso->getCantidad();
            $proyecto->setIngresosTotales(($ingresosTotales-$ingresoAntiguo)+$ingresoNuevo);
            $proyecto->setIngresosTotales($ingresoNuevo);
            $em->persist($ingreso);
            $em->flush();

            $this->addFlash('exitoEditIngreso', 'Se ha editado el ingreso correctamente');
            return $this->redirectToRoute('verProyecto', ['id'=>$idProyecto,'tab'=>5]);
        }
        return $this->render('ingresos/edit.html.twig', [
            'form' => $form->createView()

        ]);
    }
}
