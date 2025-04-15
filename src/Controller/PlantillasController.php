<?php

namespace App\Controller;

use App\Entity\Contextos;
use App\Entity\Plantillas;
use App\Form\PlantillasType;
use App\Repository\PlantillasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlantillasController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route(name: 'app_plantillas_index', methods: ['GET'])]
    public function index(PlantillasRepository $templatesRepository): Response
    {
        return $this->render('plantillas/index.html.twig', [
            'templates' => $templatesRepository->findAll(),
        ]);
    }

    #[Route('api/createTemplate', name: 'app_plantillas_new', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['code']) || !isset($data['data'])) {
            return new JsonResponse(['error' => 'Datos incompletos'], 400);
        }

        $plantilla = new Plantillas();
        $plantilla->setCode($data['code']);

        if (isset($data['data'])) {
            $plantilla->setData($data['data']);
        }

        if (isset($data['idContext'])) {
            $contexto = $entityManager->getRepository(Contextos::class)->find($data['idContext']);
            if (!$contexto) {
                return new JsonResponse(['error' => 'Contexto no encontrado'], 404);
            }
            $plantilla->setIdcontext($contexto);
        }

        $entityManager->persist($plantilla);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Plantilla creada'], 201);
    }


    #[Route('api/showTemplate/{id}', name: 'app_plantillas_show', methods: ['GET'])]
    public function showTemplateById(Plantillas $plantilla): Response
    {
        $data = [
            'id' => $plantilla->getId(),
            'code' => $plantilla->getCode(),
            'data' => $plantilla->getData(),
            'idcontext' => $plantilla->getIdcontext(),
        ];
        return new JsonResponse($data);
    }


    #[Route('/api/listTemplate/{id}', name: 'list_templates_show', methods: ['GET'])]
    public function listTemplate(int $id): JsonResponse
    {
        try {
            $templates = $this->listTemplatesByContext($id);
            return new JsonResponse($templates, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al procesar las plantillas de contexto especifico',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listTemplatesByContext(int $idContext): array
    {
        $contexto = $this->entityManager->getRepository(Contextos::class)->find($idContext);
        if (!$contexto) {
            throw new \Exception("Contexto con id $idContext no existe.");
        }

        $templates = [];
        $plantillas = $contexto->getPlantillas();

        foreach ($plantillas as $plantilla) {
            $templates[] = [
                'id' => $plantilla->getId(),
                'code' => $plantilla->getCode(),
                'data' => $plantilla->getData(),
                'idContext' => $contexto->getId()
            ];
        }
        return $templates;
    }

    #[Route('api/updateTemplate/{id}', name: 'app_plantillas_edit', methods: ['PATCH'])]
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

    #[Route('api/deleteTemplate/{id}', methods: ['DELETE'], name: 'templates_delete')]
    public function delete(Plantillas $plantilla, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($plantilla);
            $em->flush();
            return new JsonResponse(['status' => 'Plantilla eliminada'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al eliminar la plantilla',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
