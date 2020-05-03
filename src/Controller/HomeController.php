<?php

namespace App\Controller;

use App\Entity\WondArt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        $wondarts = $this->getDoctrine()->getRepository(WondArt::class)->findAll();

        return $this->render('home/index.html.twig', [
            'wondarts' => $wondarts,
        ]);
    }
}
