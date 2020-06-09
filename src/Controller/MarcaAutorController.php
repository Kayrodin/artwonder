<?php

namespace App\Controller;

use App\Entity\MarcaAutor;
use App\Entity\Usuario;
use App\Form\MarcaAutorType;
use App\Repository\MarcaAutorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/marca")
 */
class MarcaAutorController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/", name="marca_autor_index", methods={"GET","POST"})
     */
    public function index(MarcaAutorRepository $marcaAutorRepository): Response
    {
        $user = $this->getUser();
        return $this->render('marca_autor/index.html.twig', [
            'marca_autors' => $marcaAutorRepository->findAllByMy($user->getId()),
            'usuario' => $user,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/api/new", name="marca_autor_new",  options={"expose"=true}, methods={"GET","POST"})
     */
    public function new_api(Request $request)
    {
        $marcaAutor = new MarcaAutor();

        $user = $this->getUser();
        if (!$user instanceof Usuario) {
            throw new AccessDeniedException("Usuario, no autorizado");
        }
        $form = $this->createForm(MarcaAutorType::class, $marcaAutor, array(
            'action' => $this->generateUrl('marca_autor_new')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marcaAutor->setPropietario($user);
            $marcaAutor->setNombre(strtoupper($marcaAutor->getNombre()));
            $entityManager = $this->getDoctrine()->getManager();

            if($entityManager->getRepository(MarcaAutor::class)->findByName($marcaAutor->getNombre())){
                $this->addFlash(
                    'notice',
                    'Ya existe esta Marca de Autor'
                );
                return $this->redirectToRoute('marca_autor_index');
            }else{
                $entityManager->persist($marcaAutor);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Se ha creado una nueva marca de autor'
                );
                return $this->redirectToRoute('marca_autor_index');
            }

        }

        $response = array(
            "code" => 200,
            "html" =>  $this->render('marca_autor/new.html.twig', [
                'marca_autor' => $marcaAutor,
                'form' => $form->createView(),
            ])->getContent());

        return new JsonResponse($response);
    }

    /**
     * @Route("/api/show/{id}", name="marca_autor_show", options={"expose"=true}, methods={"GET"})
     */
    public function show(MarcaAutor $marcaAutor): JsonResponse
    {
        $response = array(
            "code" => 200,
            "html" =>  $this->render('marca_autor/show.html.twig', [
                'marca_autor' => $marcaAutor,
            ])->getContent());

        return new JsonResponse($response);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/api/edit/{id}", name="marca_autor_edit",  options={"expose"=true}, methods={"GET","POST"})
     */
    public function edit(Request $request, MarcaAutor $marcaAutor)
    {
        $id = $request->get('id');
        $brandOwner = $this->getDoctrine()->getRepository(MarcaAutor::class)->find($id)->getPropietario();
        $user = $this->getUser();
        if (!$user instanceof Usuario) {
            throw new AccessDeniedException("Usuario, no autorizado");
        }
        if ($user->getId() != $brandOwner->getId()) {
            throw new AccessDeniedException("Usuario, no autorizado");
        }

        $form = $this->createForm(MarcaAutorType::class, $marcaAutor, array(
            'action' => $this->generateUrl('marca_autor_edit',  array('id' => $id))));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marcaAutor->setNombre(strtoupper($marcaAutor->getNombre()));
            if($this->getDoctrine()->getRepository(MarcaAutor::class)->findByName($marcaAutor->getNombre())){
                $this->addFlash(
                    'notice',
                    'Ya existe esta Marca de Autor'
                );
                return $this->redirectToRoute('marca_autor_index');
            }else{
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'notice',
                'Su marca de autor ha sido actualizada'
            );

            return $this->redirectToRoute('marca_autor_index');
            }
        }

        $response = array(
            "code" => 200,
            "html" => $this->render('marca_autor/edit.html.twig', [
                'marca_autor' => $marcaAutor,
                'form' => $form->createView(),
            ])->getContent());

        return new JsonResponse($response);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}", name="marca_autor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MarcaAutor $marcaAutor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marcaAutor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($marcaAutor);
            $entityManager->flush();
        }

        $this->addFlash(
            'notice',
            'Su marca de autor ha sido eliminada'
        );
        return $this->redirectToRoute('marca_autor_index');
    }
}
