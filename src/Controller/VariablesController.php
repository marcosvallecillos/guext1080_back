<?php

namespace App\Controller;

use App\Entity\Contextos;
use App\Entity\Variables;
use App\Repository\VariablesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class VariablesController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(name: 'app_variables_index', methods: ['GET'])]
    public function index(VariablesRepository $variablesRepository): Response
    {
        return $this->render('variables/index.html.twig', [
            'variables' => $variablesRepository->findAll(),
        ]);
    }

    #[Route('/api/showVariables/{idContext}', name: 'app_variables_show', methods: ['GET'])]
    public function showVariables(int $idContext): JsonResponse
    {
        try {
            $variables = $this->showVariablesByContext($idContext);
            return new JsonResponse($variables, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al procesar las variables de contexto especifico',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showVariablesByContext(int $idContext): array
    {
        $contexto = $this->entityManager->getRepository(Contextos::class)->find($idContext);
        if (!$contexto) {
            throw new \Exception("Contexto con id $idContext no existe.");
        }

        $variables = [];
        foreach ($contexto->getVariables() as $variableContext) {
            $variables[] = [
                'dataVariable' => [
                    'id' => $variableContext->getId(),
                    'code' => $variableContext->getCode(),
                ],
                "idContext" => $contexto->getId(),
            ];
        }
        return $variables;
    }
}
