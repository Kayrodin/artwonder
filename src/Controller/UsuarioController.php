<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\ChangePassType;
use App\Form\PasswordType;
use App\Form\RegistrationFormType;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/usuario")
 */
class UsuarioController extends AbstractController
{
//    /**
//     * @Route("/", name="usuario_index", methods={"GET"})
//     */
//    public function index(UsuarioRepository $usuarioRepository): Response
//    {
//        return $this->render('usuario/index.html.twig', [
//            'usuarios' => $usuarioRepository->findAll(),
//        ]);
//    }
//
//    /**
//     * @Route("/new", name="usuario_new", methods={"GET","POST"})
//     */
//    public function new(Request $request): Response
//    {
//        $usuario = new Usuario();
//        $form = $this->createForm(UsuarioType::class, $usuario);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($usuario);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('usuario_index');
//        }
//
//        return $this->render('usuario/new.html.twig', [
//            'usuario' => $usuario,
//            'form' => $form->createView(),
//        ]);
//    }
//
//    /**
//     * @Route("/show/{id}", name="usuario_show", methods={"GET"})
//     */
//    public function show(Usuario $usuario): Response
//    {
//        return $this->render('usuario/show.html.twig', [
//            'usuario' => $usuario,
//        ]);
//    }

    /**
     * @Route("/edit", name="usuario_edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {
        $usuario = $this->getUser();
        $form = $this->createForm(RegistrationFormType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/changepassw", name="usuario_edit_password", methods={"GET","POST"})
     */
    public function edit_password(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $usuario = $this->getUser();
        $form = $this->createForm(ChangePassType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->setPassword(
                $passwordEncoder->encodePassword(
                    $usuario,
                    $form->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usuario_edit');
        }

        return $this->render('usuario/password.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Usuario $usuario): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($usuario);
            $entityManager->flush();
        }

        return $this->redirectToRoute('usuario_index');
    }
}
