<?php

namespace App\Controller;

use App\Entity\MarcaAutor;
use App\Entity\Usuario;
use App\Entity\WondArt;
use App\Form\WondArtType;
use App\Repository\WondArtRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/wondart")
 */
class WondArtController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/", name="wond_art_index", methods={"GET"})
     */
    public function index(WondArtRepository $wondArtRepository): Response
    {
        $user = $this->getUser();
        return $this->render('wond_art/index.html.twig', [
            'wond_arts' => $wondArtRepository->findAllByMy($user->getId()),
            'usuario' => $user,
        ]);
    }

    /**
     *  @IsGranted("ROLE_USER")
     * @Route("/api/new", name="wond_art_new",  options={"expose"=true}, methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader)
    {
        $wondArt = new WondArt();
        $user = $this->getUser();
        if (!$user instanceof Usuario) {
            throw new AccessDeniedException("Usuario, no autorizado");
        }

        $form = $this->createForm(WondArtType::class, $wondArt, array(
            'action' => $this->generateUrl('wond_art_new'),
            'edited' => true,
        ));
        $form->handleRequest($request);

        $error = $form->getErrors();

        if ($form->isSubmitted()){
            if ($form->isValid()) {
                //Securiza la creación de wondarts
                $marcaAutorId = $wondArt->getMarcaAutor()->getId();
                $brandOwnerId = $this->getDoctrine()->getRepository(MarcaAutor::class)->find($marcaAutorId)->getPropietario()->getId();
                if ($brandOwnerId != $user->getId()) {
                    throw new AccessDeniedException("Usuario, no autorizado");
                }

                $wondArt->setPublicado(false);
                $media = $form['media']->getData();
                $wondArt->setMedia($fileUploader->upload($media));//sube la imagen

                $etiquetas = $form['etiquetas']->getData();
                $wondArt->setEtiquetas($etiquetas);
                $wondArt->setFecha(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($wondArt);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Se ha creado un nuevo wondart'
                );

                return $this->redirectToRoute('wond_art_index');
            }
        }

        $response = array(
            "code" => 200,
            "html" => $this->render('wond_art/new.html.twig', [
                'wond_art' => $wondArt,
                'form' => $form->createView(),
            ])->getContent());

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}", name="wond_art_show", methods={"GET"})
     */
    public function show(WondArt $wondArt): Response
    {
        $marcaOwner = null;
        $isOwner = false;
        $marcaAutor = $wondArt->getMarcaAutor();
        $user = 'Perfil';
        if ($this->getUser()){
            $user = $this->getUser();
            $propietario = $marcaAutor->getPropietario();
            $marcaOwner = $propietario->getId();

            $isOwner = ($user->getId() == $marcaOwner);
        }

        return $this->render('wond_art/show.html.twig', [
            'wond_art' => $wondArt,
            'isOwner' => $isOwner,
            'marcaId' => $marcaAutor->getId(),
            'marcaName' => $marcaAutor->getNombre(),
            'usuario' => $user,
        ]);
    }

    /**
     *  @IsGranted("ROLE_USER")
     * @Route("/api/edit/{id}", name="wond_art_edit", options={"expose"=true}, methods={"GET","POST"})
     */
    public function edit(Request $request, WondArt $wondArt, FileUploader $fileUploader)
    {
        $id = $request->get('id');
        $form = $this->createForm(WondArtType::class, $wondArt,  array(
            'action' => $this->generateUrl('wond_art_edit',  array('id' => $id)),
            'edited' => false,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($newImg = $form['media']->getData()){
                $wondArt->setMedia($fileUploader->upload($newImg));
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'notice',
                'Su wondart ha sido actualizado'
            );

            return $this->redirectToRoute('wond_art_index');
        }

        $response = array(
            "code" => 200,
            "html" =>  $this->render('wond_art/edit.html.twig', [
                'wond_art' => $wondArt,
                'form' => $form->createView(),
            ])->getContent());

        return new JsonResponse($response);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/post/{id}", name="wond_art_post", methods={"PUBLIC"})
     */
    public function post(Request $request, WondArt $wondArt): Response
    {
        if ($this->isCsrfTokenValid('post'.$wondArt->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            //$newImg = $publicator->toPost($fileUploader->getTargetDirectory().'/'.$wondArt->getMedia());
            $wondArt->setPublicado(true);
            $entityManager->flush();
        }

        return $this->redirectToRoute('wond_art_index');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/depost/{id}", name="wond_art_depost", methods={"UNPUBLIC"})
     */
    public function depost(Request $request, WondArt $wondArt): Response
    {
        if ($this->isCsrfTokenValid('depost'.$wondArt->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $wondArt->setPublicado(false);
            $entityManager->flush();
        }

        return $this->redirectToRoute('wond_art_index');
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

        $this->addFlash(
            'notice',
            'Su wondart ha sido eliminado'
        );

        return $this->redirectToRoute('wond_art_index');
    }
}
