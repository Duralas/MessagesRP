<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de l'accueil de l'application.
 *
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * [TWIG] Affiche le template de l'accueil de l'application.
     *
     * @Route("/", name="app_home", methods={"GET"})
     *
     * @return Response Template twig accueil
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
}
