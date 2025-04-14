<?php

namespace App\Controller;

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
    public function index(ContextosRepository $contextosRepository): JsonResponse
    {
        $contextos = $contextosRepository->findAll();
        $data = [];
        foreach ($contextos as $contexto) {
            $data[] = [
                'id' => $contexto->getId(),
                'code' => $contexto->getCode(),
                'code_translate' => $contexto->getCodeTranslate(),
                'variables' => $contexto->getVariables() ?? [],
                'plantillas' => $contexto->getPlantillas() ?? [],
            ];
        }
        return new JsonResponse($data);
    }
}
