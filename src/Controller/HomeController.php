<?php

namespace App\Controller;

use App\Entity\WondArt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request)
    {
        $searchQuery = $request->get('query');

        $limit = 10;
        return $this->render('home/index.html.twig', [
            'limit'=> $limit,
            'search' => $searchQuery,
        ]);
    }

    /**
     * @Route("/api/home/{from}/{limit}/{search}", name="get_wondarts_search", options={"expose"=true}, methods={"GET","POST"}), requirements={"from"="\d+", "limit"="\d+", "search"="[a-b ]+"}
     * @param Request $request
     * @return JsonResponse
     */
    public function getWondartsSearch(Request $request): JsonResponse
    {
        $from = $request->get('from');
        $limit = $request->get('limit');
        $search = $request->get('search');

        $wondarts = $this->getDoctrine()->getRepository(WondArt::class)->findPageRangeWithSearch($from, $limit, $search);

        $islastpage = (count($wondarts)==0);

        $response = $this->render('home/wondart_structure.html.twig', [
            'wondarts' => $wondarts,
        ]);

        $response = array(
            "code" => 200,
            "lastpage" => $islastpage,
            "html" => $this->render('home/wondart_structure.html.twig', [
                'wondarts' => $wondarts
            ])->getContent() );

        return new JsonResponse($response);
    }

    /**
     * @Route("/api/home/{from}/{limit}", name="get_wondarts", options={"expose"=true}, methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getWondarts(Request $request): JsonResponse
    {
        //$stopwatch = new Stopwatch();
        //$stopwatch->start('prueba');
        $from = $request->get('from');
        $limit = $request->get('limit');

        $wondarts = $this->getDoctrine()->getRepository(WondArt::class)->findPageRange($from, $limit);
        $islastpage = (count($wondarts)==0);
        $response = $this->render('home/wondart_structure.html.twig', [
            'wondarts' => $wondarts,
        ]);
        //$stopwatch->lap('prueba');
        //$event = $stopwatch->stop('prueba');
        $response = array(
            "code" => 200,
            "lastpage" => $islastpage,
            "html" => $this->render('home/wondart_structure.html.twig', [
                'wondarts' => $wondarts
            ])->getContent() );
        return new JsonResponse($response);
    }
}
