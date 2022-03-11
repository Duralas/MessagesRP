<?php

declare(strict_types=1);

namespace App\Controller\Census\Mass\Seizure;

use App\Form\ModelType\Census\MassSeizureType;
use App\Model\MassSeizureCensusModel;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recensement/masse/saisie', name: 'app_census_mass_seizure_post', methods: ['POST'])]
final class PostController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        $massSeizureModel = new MassSeizureCensusModel(new ArrayCollection());
        $form = $this->createForm(MassSeizureType::class, $massSeizureModel);
        $form->handleRequest($request);

        dump($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render(
            'census/mass/seizure/index.html.twig',
            [
                'form' => $form->createView(),
                'period_month_list' => [],
            ]
        );
    }
}
