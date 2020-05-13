<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\MarcaAutor;
use App\Entity\Usuario;
use App\Entity\WondArt;
use App\Form\WondArtType;
use App\Repository\WondArtRepository;
use App\Service\FileUploader;
use App\Service\Publicator;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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
        $userId = $this->getUser()->getId();
        return $this->render('wond_art/index.html.twig', [
            'wond_arts' => $wondArtRepository->findAllByMy($userId),
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
            'action' => $this->generateUrl('wond_art_new')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Securiza la creación de wondarts
            $marcaAutorId = $wondArt->getMarcaAutor()->getId();
            $brandOwnerId = $this->getDoctrine()->getRepository(MarcaAutor::class)->find($marcaAutorId)->getPropietario()->getId();
            if($brandOwnerId != $user->getId()){
                throw new AccessDeniedException("Usuario, no autorizado");
            }

            $wondArt->setPublicado(false);
            $wondArt->setMedia($fileUploader->upload($form['media']->getData()));//sube la imagen

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($wondArt);
            $entityManager->flush();
            return $this->redirectToRoute('wond_art_index');
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
        return $this->render('wond_art/show.html.twig', [
            'wond_art' => $wondArt,
        ]);
    }

    /**
     *  @IsGranted("ROLE_USER")
     * @Route("/api/edit/{id}", name="wond_art_edit", options={"expose"=true}, methods={"GET","POST"})
     */
    public function edit(Request $request, WondArt $wondArt, CacheManager $cacheManager)
    {
        $id = $request->get('id');
        $form = $this->createForm(WondArtType::class, $wondArt,  array(
            'action' => $this->generateUrl('wond_art_edit',  array('id' => $id)),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
    public function post(Request $request, WondArt $wondArt, Publicator $publicator, FileUploader $fileUploader): Response
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
