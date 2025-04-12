<?php

namespace App\Controller;

use App\Entity\Variables;
use App\Form\VariablesType;
use App\Repository\VariablesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/variables')]
final class VariablesController extends AbstractController
{
    #[Route(name: 'app_variables_index', methods: ['GET'])]
    public function index(VariablesRepository $variablesRepository): Response
    {
        return $this->render('variables/index.html.twig', [
            'variables' => $variablesRepository->findAll(),
        ]);
    }



    #[Route('/showVariables', name: 'app_variables_show', methods: ['GET'])]
    public function show(Variables $variable): Response
    {
        return $this->render('variables/show.html.twig', [
            'variable' => $variable,
        ]);
    }


}
