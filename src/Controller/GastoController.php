<?php

namespace App\Controller;
use App\Entity\Gasto;
use App\Entity\Sedes;
use App\Form\GastoType;
use App\Form\SedeType;
use App\Repository\ProyectosRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine;

class GastoController extends AbstractController
{
    #[Route('/crearGasto/{idProyecto}', name: 'crear_gasto')]
    public function index(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, ProyectosRepository $proyectos, $idProyecto): Response
    {
        $gasto = new Gasto();
        $form= $this->createForm(GastoType::class, $gasto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em= $doctrine->getManager();
            $proyecto=$proyectos->findOneBy(array('id' => $idProyecto));
            $gasto->setProyecto($proyecto);
            $gastoTotal=$proyecto->getTotalGasto();
            $gastoTotalNuevo=$gastoTotal+($gasto->getImporte());
            $proyecto->setTotalGasto($gastoTotalNuevo);
            $em->persist($gasto);
            $em->flush();

            $this->addFlash('exitoGasto', 'Se ha creado el gasto correctamente');
            return $this->redirectToRoute('verProyecto', ['id'=>$idProyecto, 'tab'=>5]);
        }
        return $this->render('gasto/index.html.twig', [
            'form' => $form->createView()

        ]);
    }

    #[Route('/editarGasto/{id}/{idProyecto}', name: 'editar_gasto')]
    public function editGasto(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, ProyectosRepository $proyectos,  Gasto $gasto, $idProyecto): Response
    {
        $gastoAntiguoImporte=$gasto->getImporte();
        $form= $this->createForm(GastoType::class, $gasto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em= $doctrine->getManager();
            $proyecto=$proyectos->findOneBy(array('id' => $idProyecto));
            $gasto->setProyecto($proyecto);
            $gastoTotal=$proyecto->getTotalGasto()-$gastoAntiguoImporte;
            $gastoTotalNuevo=$gastoTotal+($gasto->getImporte());
            $proyecto->setTotalGasto($gastoTotalNuevo);
            $em->persist($gasto);
            $em->flush();

            $this->addFlash('exitoEditGasto', 'Se ha editado el gasto correctamente');
            return $this->redirectToRoute('verProyecto', ['id'=>$idProyecto, 'tab'=>5]);
        }
        return $this->render('gasto/edit.html.twig', [
            'form' => $form->createView()

        ]);
    }
}
