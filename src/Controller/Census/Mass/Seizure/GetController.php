<?php

declare(strict_types=1);

namespace App\Controller\Census\Mass\Seizure;

use App\{
    Form\ModelType\Census\MassType,
    Model\MassCensusModel
};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recensement/masse/saisie', name: 'app_census_mass_seizure_get', methods: ['GET'])]
final class GetController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        $massCensusModel = new MassCensusModel();
        $form = $this->createForm(MassType::class, $massCensusModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render('index.html.twig');
        }

        return $this->render('census/mass/index.html.twig', ['form' => $form->createView()]);
    }
}
