<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function __invoke(Request $request): Response
    {
        try {
            $data = $request->getContent();
            $email = $data['email'];
            $password = $data['password'];

            if (!isset($email) || !isset($password)) {
                throw new BadRequestException('Parametros incorrectos');
            }
            
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new Response('Usuario creado', Response::HTTP_OK);

        } catch (\Exception $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
      }
    }
}