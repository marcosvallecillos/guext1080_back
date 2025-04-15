<?php

namespace App\Controller;

use App\Repository\IdiomasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IdiomasController extends AbstractController
{
    #[Route(path: '/api/languages', name: 'app_idiomas')]
    public function index(IdiomasRepository $languagesRepository): JsonResponse
    {
        $languages = $languagesRepository->findAll();
        
        $data = [];
        foreach ($languages as $language) {
            $data[] = [
                'id' => $language->getId(),
                'code' => $language->getCode(),
                'value' => $language->getName(),
            ];
        }
        return new JsonResponse($data);
    }
}
