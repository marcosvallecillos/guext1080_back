<?php

namespace App\Controller;

use App\Entity\Contextos;
use App\Repository\ContextosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContextosController extends AbstractController
{
    #[Route('/api/contexts', name: 'app_contextos_index')]
    public function index(ContextosRepository $contextsRepository): JsonResponse
    {
        try {
            $contexts = $contextsRepository->findAll();
            $data = [];

            foreach ($contexts as $context) {
                $data[] = [
                    'id' => $context->getId(),
                    'code' => $context->getCode(),
                ];
            }
            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'mensaje' => 'Error al obtener los datos del contexto',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
