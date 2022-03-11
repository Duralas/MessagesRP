<?php

declare(strict_types=1);

namespace App\Controller\Census\Mass\Seizure;

use App\{
    Form\ModelType\Census\MassSeizureType,
    Form\ModelType\Census\MassType,
    Model\CensusMonthSeizureModel,
    Model\MassCensusModel,
    Model\MassSeizureCensusModel,
    Model\SeizureCensusModel,
    Repository\MoisRepository
};
use Doctrine\Common\Collections\ArrayCollection;
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
            $massSeizureModel = (new MassSeizureCensusModel())
                ->setZone($massCensusModel->getZone());

            $periodMonths = $moisRepository->findForPeriode($massCensusModel->getStrictPeriode());
            $censusMonthSeizureList = new ArrayCollection();
            foreach ($periodMonths as $mois) {
                $censusMonthSeizureList->add(new CensusMonthSeizureModel($mois));
            }

            foreach ($massCensusModel->getPersonnages() as $personnage) {
                $massSeizureModel
                    ->getSeizureCensusList()
                    ->add(
                        new SeizureCensusModel($personnage, clone $censusMonthSeizureList)
                    );
            }

            return $this->render(
                'census/mass/seizure/index.html.twig',
                [
                    'form' => $this
                        ->createForm(MassSeizureType::class, $massSeizureModel)
                        ->createView(),
                    'period_month_list' => $periodMonths,
                ]
            );
        }

        return $this->render('census/mass/index.html.twig', ['form' => $form->createView()]);
    }
}
