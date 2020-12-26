<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\RecenseSearchType;
use App\Form\RecenseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur permettant le recensement des messages RP pour réaliser les rapports du Phénix Enchaîné.
 *
 * @package App\Controller
 */
class RecensementController extends AbstractController
{
    /**
     * [TWIG] Affiche la page permettant de recenser des messages RP.
     *
     * @Route("/recensement", name="app_recensement", methods={"GET"})
     *
     * @return Response Template twig recensement
     */
    public function index(): Response
    {
        // Formulaire de recherche
        $formSearch = $this->createForm(RecenseSearchType::class);

        // Formulaire de recensement
        $formRecense = $this->createForm(RecenseType::class);
        
        return $this->render('recensement/index.html.twig', array(
            'form_recherche'   => $formSearch->createView(),
            'form_recensement' => $formRecense->createView(),
        ));
    }

    /**
     * [Redirect|TWIG] Gère le submit du formulaire de recensement.
     *
     * Enregistre le recensement ou retourne les erreurs du formulaire.
     *
     * @Route("/recensement", name="app_recensement_handle", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function recense(Request $request): Response
    {
        // Formulaire de recensement
        $recensement = new Message();
        $formRecense = $this->createForm(RecenseType::class, $recensement);
        $formRecense->handleRequest($request);

        if ($formRecense->isSubmitted() && $formRecense->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recensement);
            $entityManager->flush();

            return $this->redirectToRoute('app_recensement');
        }

        return $this->render('recensement/index.html.twig', array(
            'form_recherche'   => $this->createForm(RecenseSearchType::class)->createView(),
            'form_recensement' => $formRecense->createView(),
        ));
    }
}
