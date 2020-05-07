<?php

namespace App\Controller;

use App\Entity\WondArt;
use App\Form\WondArtType;
use App\Repository\WondArtRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wondart")
 */
class WondArtController extends AbstractController
{
    /**
     *  @IsGranted("ROLE_USER")
     * @Route("/", name="wond_art_index", methods={"GET"})
     */
    public function index(WondArtRepository $wondArtRepository): Response
    {
        $userId = $this->getUser()->getId();
        return $this->render('wond_art/index.html.twig', [
            'wond_arts' => $wondArtRepository->findAllByMy($userId),
        ]);
    }

    /**
     *  @IsGranted("ROLE_USER")
     * @Route("/new", name="wond_art_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $wondArt = new WondArt();
        $form = $this->createForm(WondArtType::class, $wondArt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wondArt->setMedia($fileUploader->upload($form['media']->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wondArt);
            $entityManager->flush();
            return $this->redirectToRoute('wond_art_index');
        }

        return $this->render('wond_art/new.html.twig', [
            'wond_art' => $wondArt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wond_art_show", methods={"GET"})
     */
    public function show(WondArt $wondArt): Response
    {
        return $this->render('wond_art/show.html.twig', [
            'wond_art' => $wondArt,
        ]);
    }

    /**
     *  @IsGranted("ROLE_USER")
     * @Route("/{id}/edit", name="wond_art_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, WondArt $wondArt): Response
    {
        $form = $this->createForm(WondArtType::class, $wondArt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('wond_art_index');
        }

        return $this->render('wond_art/edit.html.twig', [
            'wond_art' => $wondArt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}", name="wond_art_delete", methods={"DELETE"})
     */
    public function delete(Request $request, WondArt $wondArt): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wondArt->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($wondArt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('wond_art_index');
    }
}
