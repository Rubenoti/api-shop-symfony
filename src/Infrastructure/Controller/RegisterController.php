<?php

namespace App\Infrastructure\Controller;

use App\Application\Register\RegisterUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends AbstractController
{
    
    public function __construct(
        private RegisterUserService $service
    ) {
    }
   
    public function __invoke(Request $request): Response
    {
        try {
            $data = $request->getContent();
            $result = json_decode($data, true);

            if (!isset($result['email']) || !isset($result['password'])) {
                throw new BadRequestException('Parametros incorrectos');
            }

            $request->headers->all();

            $email = $result['email'];
            $password = $result['password'];

            $this->service->createUser($email, $password);
            
            return new Response('Usuario creado', Response::HTTP_OK);

        } catch (\Exception $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
      }
    }
}