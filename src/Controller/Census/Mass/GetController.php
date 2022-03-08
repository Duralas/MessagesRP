<?php

declare(strict_types=1);

namespace App\Controller\Census\Mass;

use App\{
    Form\ModelType\Census\MassType,
    Model\MassCensusModel,
    Repository\PeriodeRepository
};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'recensement/masse', name: 'app_census_mass_get', methods: ['GET'])]
final class GetController extends AbstractController
{
    public function __invoke(PeriodeRepository $periodeRepository): Response
    {
        return $this->render(
            'census/mass/index.html.twig',
            [
                'form' => $this
                    ->createForm(
                        MassType::class,
                        (new MassCensusModel())
                            ->setPeriode($periodeRepository->findActive())
                    )
                    ->createView(),
            ]
        );
    }
}
