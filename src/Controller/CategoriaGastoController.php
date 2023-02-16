<?php

namespace App\Controller;
use App\Entity\CategoriaGasto;
use App\Form\CategoriaGastoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine;

class CategoriaGastoController extends AbstractController
{
    #[Route('/crearCategoriaGasto/{idProyecto}', name: 'crear_CategoriaGasto')]
    public function index(Request $request, Doctrine\Persistence\ManagerRegistry $doctrine, $idProyecto=""): Response
    {
        $CategoriaGasto = new CategoriaGasto();
        $form= $this->createForm(CategoriaGastoType::class, $CategoriaGasto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em= $doctrine->getManager();
            $em->persist($CategoriaGasto);
            $em->flush();

            $this->addFlash('exitoCategoriaGasto', 'Se ha creado la categoría para un gasto correctamente');
            if($idProyecto==""){
                return $this->redirectToRoute('app_dashboard');
            }else{
                return $this->redirectToRoute('verProyecto',['id'=>$idProyecto, 'tab'=>5]);

            }

        }         $this->addFlash('falloCategoriaGasto', 'No se ha podido crear el CategoriaGasto');
        return $this->render('categoria_gasto/index.html.twig', [
            'form' => $form->createView()

        ]);
    }
}
