<?php

namespace App\Controller;

use App\Entity\MarcaAutor;
use App\Entity\Usuario;
use App\Form\MarcaAutorType;
use App\Repository\MarcaAutorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/marca")
 */
class MarcaAutorController extends AbstractController
{
    /**
     * @Route("/", name="marca_autor_index", methods={"GET"})
     */
    public function index(MarcaAutorRepository $marcaAutorRepository): Response
    {
        $userId = $this->getUser()->getId();
        return $this->render('marca_autor/index.html.twig', [
            'marca_autors' => $marcaAutorRepository->findAllByMy($userId),
        ]);
    }

    /**
     * @Route("/new", name="marca_autor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $marcaAutor = new MarcaAutor();
        $form = $this->createForm(MarcaAutorType::class, $marcaAutor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $marcaAutor->setPropietario($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($marcaAutor);
            $entityManager->flush();

            return $this->redirectToRoute('marca_autor_index');
        }

        return $this->render('marca_autor/new.html.twig', [
            'marca_autor' => $marcaAutor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="marca_autor_show", methods={"GET"})
     */
    public function show(MarcaAutor $marcaAutor): Response
    {
        return $this->render('marca_autor/show.html.twig', [
            'marca_autor' => $marcaAutor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="marca_autor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MarcaAutor $marcaAutor): Response
    {
        $form = $this->createForm(MarcaAutorType::class, $marcaAutor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marca_autor_index');
        }

        return $this->render('marca_autor/edit.html.twig', [
            'marca_autor' => $marcaAutor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="marca_autor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MarcaAutor $marcaAutor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marcaAutor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($marcaAutor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('marca_autor_index');
    }
}
