<?php

namespace App\Controller\CRUD;

use App\{
    Entity\Personnage,
    Form\CRUD\PersonnageType,
    Repository\PersonnageRepository
};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personnage")
 */
class PersonnageController extends AbstractController
{
    /**
     * @Route("/", name="personnage_index", methods={"GET"})
     */
    public function index(PersonnageRepository $personnageRepository): Response
    {
        return $this->render(
            'personnage/index.html.twig',
            [
                'personnages' => $personnageRepository->getAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="personnage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $personnage = new Personnage();
        $form = $this->createForm(PersonnageType::class, $personnage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personnage);
            $entityManager->flush();

            return $this->redirectToRoute('personnage_index');
        }

        return $this->render('personnage/new.html.twig', [
            'personnage' => $personnage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{nom}", name="personnage_show", methods={"GET"})
     */
    public function show(Personnage $personnage): Response
    {
        return $this->render('personnage/show.html.twig', [
            'personnage' => $personnage,
        ]);
    }

    /**
     * @Route("/{nom}/edit", name="personnage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Personnage $personnage): Response
    {
        $form = $this->createForm(PersonnageType::class, $personnage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('personnage_index');
        }

        return $this->render('personnage/edit.html.twig', [
            'personnage' => $personnage,
            'form' => $form->createView(),
        ]);
    }
}
