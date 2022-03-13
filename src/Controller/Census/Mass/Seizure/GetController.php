<?php

declare(strict_types=1);

namespace App\Controller\Census\Mass\Seizure;

use App\{
    Entity\Personnage,
    Form\ModelType\Census\MassSeizureType,
    Form\ModelType\Census\MassType,
    Model\CensusMonthSeizureModel,
    Model\MassCensusModel,
    Model\MassSeizureCensusModel,
    Model\SeizureCensusModel,
    Repository\MoisRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recensement/masse/saisie', name: 'app_census_mass_seizure_get', methods: ['GET'])]
final class GetController extends AbstractController
{
    public function __invoke(MoisRepository $moisRepository, Request $request): Response
    {
        $massCensusModel = new MassCensusModel();
        $form = $this->createForm(MassType::class, $massCensusModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $periodMonths = $moisRepository->findForPeriode($massCensusModel->getStrictPeriode());
            $censusMonthSeizureList = [];
            foreach ($periodMonths as $mois) {
                $censusMonthSeizureList[] = (new CensusMonthSeizureModel())->setMonth($mois);
            }

            $sortedPersonnages = $massCensusModel->getPersonnages()->toArray();
            usort(
                $sortedPersonnages,
                static fn (Personnage $personnageA, Personnage $personnageB): int => (
                    $personnageA->getNom() <=> $personnageB->getNom()
                )
            );
            $seizureCensusList = [];
            foreach ($sortedPersonnages as $personnage) {
                $seizureCensusList[] = (new SeizureCensusModel())
                    ->setPersonnage($personnage)
                    ->setCensusMonthSeizures($censusMonthSeizureList);
            }

            return $this->render(
                'census/mass/seizure/index.html.twig',
                [
                    'form' => $this
                        ->createForm(
                            MassSeizureType::class,
                            (new MassSeizureCensusModel())
                                ->setPeriode($massCensusModel->getPeriode())
                                ->setZone($massCensusModel->getZone())
                                ->setSeizureCensusList($seizureCensusList)
                        )
                        ->createView(),
                    'period_month_list' => $periodMonths,
                ]
            );
        }

        return $this->render('census/mass/index.html.twig', ['form' => $form->createView()]);
    }
}
