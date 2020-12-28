<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\RecenseFilterType;
use App\Form\RecenseSearchType;
use App\Form\RecenseType;
use App\Model\RecenseFilters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $recensement = new Message();

        return $this->createRecenseView($this->createForm(RecenseSearchType::class), $this->createForm(RecenseType::class, $recensement));
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
     * @return Response|RedirectResponse Template twig recensement si erreurs sinon vue de l'index
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

        return $this->createRecenseView($this->createForm(RecenseSearchType::class, $recensement), $formRecense);
    }

    /**
     * [Redirect|TWIG] Modifie un recensement effectué et récupéré par le /search.
     *
     * Met à jour le recensement ou retourne les erreurs du formulaire.
     *
     * @Route("/recensement", name="app_recensement_edit", methods={"PATCH"})
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse Template twig recensement si erreurs sinon vue de l'index
     */
    public function update(Request $request): Response
    {
        // Formulaire de recensement
        $recensement = $this->getDoctrine()->getManager()->getRepository(Message::class)->findOneBy(array(
            'periode' => $request->get('old_periode'),
            'mois'    => $request->get('old_mois'),
            'zone'    => $request->get('old_zone'),
            'auteur'  => $request->get('old_auteur'),
        ));
        $formRecense = $this->createForm(RecenseType::class, $recensement, array(
            'method' => 'PATCH',
        ));
        $formRecense->handleRequest($request);

        if ($formRecense->isSubmitted() && $formRecense->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_recensement');
        }

        return $this->createRecenseView($this->createForm(RecenseSearchType::class, $recensement), $formRecense);
    }

    /**
     * [TWIG] Gère la recherche d'un enregistrement pour en faire une mise à jour.
     *
     * Enregistre le recensement ou retourne les erreurs du formulaire.
     *
     * @Route("/search", name="app_recensement_search", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response Template twig recensement
     */
    public function search(Request $request): Response
    {
        // Formulaire de recherche
        $recensement = new Message();
        $formSearch  = $this->createForm(RecenseSearchType::class, $recensement);
        $formSearch->handleRequest($request);

        $foundData = null;
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $foundData = $this->getDoctrine()->getManager()->getRepository(Message::class)->findOneBy(array(
                'periode' => $recensement->getPeriode(),
                'mois'    => $recensement->getMois(),
                'zone'    => $recensement->getZone(),
                'auteur'  => $recensement->getAuteur(),
            ));
        }

        return $this->createRecenseView($formSearch, $this->createForm(RecenseType::class, $foundData ?? $recensement), array(
            'recensement' => $foundData,
        ));
    }

    /**
     * [Redirect] Enregistre les filtres et redirige vers la page de recensement.
     *
     * @Route("/filter", name="app_recensement_filter", methods={"POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function filter(Request $request): RedirectResponse
    {
        // Formulaire de recherche
        $filters    = new RecenseFilters();
        $formSearch = $this->createForm(RecenseFilterType::class, $filters);
        $formSearch->handleRequest($request);

        return $this->redirectToRoute('app_recensement');
    }

    /**
     * Crée la vue de recensement avec les deux formulaires.
     *
     * @param FormInterface $formSearch  Formulaire de {@see RecenseType recherche}
     * @param FormInterface $formRecense Formulaire de {@see RecenseSearchType recensement}
     * @param array         $viewData    Données additionnelles pour la vue
     *
     * @return Response Template TWIG de recensement
     */
    private function createRecenseView(FormInterface $formSearch, FormInterface $formRecense, array $viewData = array()): Response
    {
        return $this->render('recensement/index.html.twig', array_merge(array(
            'form_recherche'   => $formSearch->createView(),
            'form_filtre'      => $this->createForm(RecenseFilterType::class)->createView(),
            'form_recensement' => $formRecense->createView(),
        ), $viewData));
    }
}
