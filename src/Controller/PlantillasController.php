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
        ];
        return new JsonResponse($data);
    }

    #[Route('api/listTemplate/{id}', name: 'list_templates_id', methods: ['GET'])]
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
            ];
        }
        return $templates;
    }

    #[Route('/api/filterInfoTemplates', name: 'filter_templates', methods: ['POST'])]
    public function filterInfoTemplates(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!is_array($data) || !isset($data[0]['pageModel'], $data[0]['filter'])) {
                return new JsonResponse(['mensaje' => 'Datos de filtrado incompletos'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $pageModel = $data[0]['pageModel'];
            $filter = $data[0]['filter'];

            $result = $this->findTemplatesByFilters($pageModel, $filter);

            return new JsonResponse([
                'templates' => $result['data'],
                'total' => $result['total']
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al procesar las plantillas con los filtros',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findTemplatesByFilters(array $pageModel, array $filter): array
    {
        $repo = $this->entityManager->getRepository(Plantillas::class);
        $qb = $repo->createQueryBuilder('p')
            ->innerJoin('p.idcontext', 'c');
        $qb->addSelect('c');

        if (!empty($filter['search'])) {
            $qb->andWhere('p.code LIKE :search')
                ->setParameter('search', $filter['search'] . '%');
        }

        if (!empty($filter['context'])) {
            if ($filter['context'] !== "Todos") {
                $qb->andWhere('c.code = :context')
                    ->setParameter('context', $filter['context']);
            }
        }

        $qbCount = clone $qb;
        $totalRecords = (int) $qbCount
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Ahora sí hacemos la paginación
        $page = max(1, (int) $pageModel['page']);
        $size = (int) $pageModel['size'];
        $qb->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size);

        if (!empty($pageModel['orderBy'])) {
            $direction = strtoupper($pageModel['orientation']);

            if ($pageModel['orderBy'] === 'context') {
                $qb->orderBy('c.code', $direction);
            } else {
                $qb->orderBy('p.' . $pageModel['orderBy'], $direction);
            }
        }

        $templates = $qb->getQuery()->getResult();

        $formattedTemplates = array_map(function ($template) {
            return [
                'id' => $template->getId(),
                'code' => $template->getCode(),
                'data' => $template->getData(),
                'context' => $template->getIdcontext()->getCode()
            ];
        }, $templates);

        return [
            'data' => $formattedTemplates,
            'total' => $totalRecords
        ];
    }

    #[Route('/api/getAllTemplates', name: 'get_allTemplates', methods: ['GET'])]
    public function getAllTemplates(): JsonResponse
    {
        try {
            $repo = $this->entityManager->getRepository(Plantillas::class);
            $qb = $repo->createQueryBuilder('p')
                ->innerJoin('p.idcontext', 'c')
                ->addSelect('c');

            $templates = $qb->getQuery()->getResult();

            $formattedTemplates = array_map(function ($template) {
                return [
                    'id' => $template->getId(),
                    'code' => $template->getCode(),
                    'data' => $template->getData(),
                    'context' => $template->getIdcontext()->getCode()
                ];
            }, $templates);

            return new JsonResponse([
                'templates' => $formattedTemplates,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al obtener las plantillas',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('api/updateTemplate/{id}', name: 'app_plantillas_edit', methods: ['PATCH'])]
    public function updateTemplatesDB(Request $request, Plantillas $plantilla, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (isset($data['data'])) {
                $plantilla->setData($data['data']);
            }
            $entityManager->flush();
            return new JsonResponse(['status' => 'Plantilla actualizada'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al actualizar la plantilla',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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

    #[Route('api/renderTemplate', name: 'render_template', methods: ['POST'])]
    public function renderTemplate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $idTemplate = $data['templateId'];
        $languageCode = $data['languageCode'];
        $dataVariables = $data['data']; // Asumimos que 'datos' es un array en el cuerpo del POST


        $plantilla = $this->entityManager->getRepository(Plantillas::class)->find($idTemplate);

        if (!$plantilla) {
            return new JsonResponse(['error' => 'No se ha encontrado la plantilla con el ID: ' . $idTemplate], 404);
        }

        $dataJson = $plantilla->getData();

        if (empty($dataJson) || !is_array($dataJson)) {
            return new JsonResponse(['error' => 'No hay datos en esta plantilla.'], 400);
        }

        // Si no se encuentra el idioma solicitado, usar el idioma por defecto (español)
        if (!isset($dataJson[$languageCode])) {
            $languageCode = 'es';
        }


        $contenido = $dataJson[$languageCode]['content'] ?? null;
        $subject = $dataJson[$languageCode]['subject'] ?? null;

        if (!$contenido) {
            return new JsonResponse(['error' => "La plantilla no tiene contenido para el idioma '$languageCode'."], 400);
        }

        // Reemplazar los placeholders en el contenido
        foreach ($dataVariables as $key => $value) {
            $contenido = str_replace("{{" . $key . "}}", $value, $contenido);
        }

        return new JsonResponse([
            'rendered' => $contenido,
            'subject' => $subject
        ]);
    }
}
