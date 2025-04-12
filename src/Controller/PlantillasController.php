<?php

namespace App\Controller;

use App\Entity\Plantillas;
use App\Form\PlantillasType;
use App\Repository\PlantillasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/plantillas')]
final class PlantillasController extends AbstractController
{
    #[Route(name: 'app_plantillas_index', methods: ['GET'])]
    public function index(PlantillasRepository $plantillasRepository): Response
    {
        return $this->render('plantillas/index.html.twig', [
            'plantillas' => $plantillasRepository->findAll(),
        ]);
    }

    #[Route('/createTemplate', name: 'app_plantillas_new', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $plantilla = new Plantillas();

        // Asignar los campos desde el JSON recibido (ajusta según tu entidad)
        $plantilla->setCode($data['code'] ?? null);
        $plantilla->setSubject($data['subject'] ?? null);
        $plantilla->setContent($data['content'] ?? null);
        $plantilla->setIdcontext($data['idContext'] ?? null);
        // Agrega más campos si tu entidad los tiene

        $entityManager->persist($plantilla);
        $entityManager->flush();

        return new Response(['status' => 'Plantilla creada'], 201);
    }

    #[Route('/showTemplate/{id}', name: 'app_plantillas_show', methods: ['GET'])]
    public function show(Plantillas $plantilla): Response
    {
        $data = [
            'id' => $plantilla->getId(),
            'code' => $plantilla->getCode(),
            'subject' => $plantilla->getSubject(),
            'content' => $plantilla->getContent(),
            'idcontext' => $plantilla->getIdcontext(),
        ];
        return new Response($data);
    }

    #[Route('/updateTemplate/{id}', name: 'app_plantillas_edit', methods: ['PATCH'])]
    public function partialUpdate(Request $request, Plantillas $plantilla, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['code'])) {
            $plantilla->setCode($data['code']);
        }
        if (isset($data['content'])) {
            $plantilla->setContent($data['content']);
        }
            $entityManager->flush();

            return new Response(['status' => 'Plantilla actualizada']);
        }


    #[Route('/plantillas/{id}', methods: ['DELETE'], name: 'plantillas_delete')]
    public function delete(Plantillas $plantilla, EntityManagerInterface $em): Response
    {
        $em->remove($plantilla);
        $em->flush();

        return new Response(['status' => 'Plantilla eliminada']);
    }
}
