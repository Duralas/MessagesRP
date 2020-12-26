<?php

namespace App\Controller\CRUD;

use App\Entity\Periode;
use App\Form\CRUD\PeriodeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/periode")
 */
class PeriodeController extends AbstractController
{
    /**
     * @Route("/", name="periode_index", methods={"GET"})
     */
    public function index(): Response
    {
        $periodes = $this->getDoctrine()
            ->getRepository(Periode::class)
            ->findAll();

        return $this->render('periode/index.html.twig', [
            'periodes' => $periodes,
        ]);
    }

    /**
     * @Route("/new", name="periode_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $periode = new Periode();
        $form = $this->createForm(PeriodeType::class, $periode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($periode);
            $entityManager->flush();

            return $this->redirectToRoute('periode_index');
        }

        return $this->render('periode/new.html.twig', [
            'periode' => $periode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="periode_show", methods={"GET"})
     */
    public function show(Periode $periode): Response
    {
        return $this->render('periode/show.html.twig', [
            'periode' => $periode,
        ]);
    }

    /**
     * @Route("/{code}/edit", name="periode_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Periode $periode): Response
    {
        $form = $this->createForm(PeriodeType::class, $periode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('periode_index');
        }

        return $this->render('periode/edit.html.twig', [
            'periode' => $periode,
            'form' => $form->createView(),
        ]);
    }
}
