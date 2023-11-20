<?php

namespace App\Infrastructure\Controller;

use App\Application\Login\LoginUserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    public function __construct(
        public readonly RequestStack $requestStack,
        private readonly LoginUserService $loginUserService,
    ) {

    }
   public function __invoke(Request $request): Response
   {
        try {
            $data = $request->getContent();
            $result = json_decode($data, true);

            if (!isset($result['email']) || !isset($result['password'])) {
                throw new Exception('Parametros incorrectos');
            }

            $email = $result['email'];
            $password = $result['password'];
            
            $user = $this->loginUserService->validateUser($email, $password);

            if(is_null($user)) {
                throw new Exception('usuario no encontrado');
            }

            return new Response('Login correcto', Response::HTTP_OK);
        } catch (Exception $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
   }
}
