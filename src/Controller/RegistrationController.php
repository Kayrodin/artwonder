<?php

namespace App\Controller;

use App\Entity\MarcaAutor;
use App\Entity\Usuario;
use App\Form\ChooseRoleType;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/choose", name="app_choose")
     */
    public function chooseRole(Request $request): Response
    {
        return $this->render('registration/chooserol.html.twig');
    }

    /**
     * @Route("/register/{rol}", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginAuthenticator $authenticator): Response
    {
        $rol = $request->get('rol');
        if(!is_null($this->getUser())){
            return $this->redirect($this->generateUrl('home'));
        }
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'rol' => $rol,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $rol = $form->get('rol')->getViewData();
            if($rol == 1){
                $user->setTipo('ROLE_USER');
                $marcaAnonima = new MarcaAutor();
                $marcaAnonima->setNombre('ANONIMO');
                $user->addMarca($marcaAnonima);
            }
            if($rol == 0){
                $user->setTipo('ROLE_AGENT');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}
