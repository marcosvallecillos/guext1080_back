<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VariablesContextoController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Inyección del EntityManager a través del constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/contexto/variables', name: 'app_contexto_variables')]
    public function index(): Response
    {
        // Llamada a la nueva función para obtener variables asociadas a un contexto (por ejemplo, con ID 1)
        try {
            $variables = $this->obtenerVariablesPorContexto(1); // Cambiar por el ID del contexto deseado
        } catch (\Exception $e) {
            // Manejo de errores si el contexto no existe o alguna otra falla
            return $this->render('contexto_variables/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }

        // Renderizar la plantilla Twig, pasando las variables obtenidas
        return $this->render('contexto_variables/index.html.twig', [
            'controller_name' => 'ContextoVariablesController',
            'variables' => $variables,  // Pasando las variables a la plantilla
        ]);
    }

    /**
     * Obtiene las variables asociadas a un contexto específico.
     *
     * @param int $contextoId El ID del contexto para el que quieres obtener las variables.
     * @return array La lista de variables asociadas al contexto.
     */
    private function obtenerVariablesPorContexto(int $contextoId): array
    {
        // Buscar el contexto por ID
        $contexto = $this->entityManager->getRepository(Contextos::class)->find($contextoId);

        if (!$contexto) {
            throw new \Exception("El contexto con ID $contextoId no existe.");
        }

        // Obtener las relaciones de ContextoVariable asociadas a este contexto
        $contextoVariables = $this->entityManager->getRepository(ContextoVariable::class)->findBy([
            'idcontexto' => $contexto
        ]);

        // Crear un array para almacenar las variables
        $variables = [];

        // Recorrer las relaciones y extraer las variables
        foreach ($contextoVariables as $contextoVariable) {
            foreach ($contextoVariable->getIdvariables() as $variable) {
                $variables[] = $variable;
            }
        }

        return $variables;
    }
}
