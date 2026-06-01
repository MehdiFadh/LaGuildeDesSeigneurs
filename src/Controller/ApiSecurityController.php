<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiSecurityController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $client
    ) {
    }


    #[Route('/api-login', name: 'api_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        // 1. Si c'est un GET, on affiche juste le formulaire de connexion vide
        if ($request->isMethod('GET')) {
            return $this->render('api-security/login.html.twig');
        }

        // 2. Si c'est un POST, on tente la connexion à l'API REST
        try {
            $response = $this->client->request(
                'POST',
                $this->getParameter('app.api_url') . '/signin', // On récupère notre paramètre
                [
                    'json' => [
                        'username' => $request->request->get('_username'), // Récupération du username
                        'password' => $request->request->get('_password') // Récupération du password
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
            if (200 === $statusCode) {
                $content = json_decode($response->getContent(), true);
                if (isset($content['token'])) {
                    // Mise en session du token
                    $request->getSession()->set('token', $content['token']);
                    $this->addFlash('success', 'Connexion réussie !');
                    return $this->redirectToRoute('api_character_index', [], Response::HTTP_SEE_OTHER);
                }
            }

            // Gestion de l'erreur retournée par l'API (ex: 401, 404, etc.)
            $errorData = json_decode($response->getContent(false), true);
            $errorMessage = $errorData['error'] ?? 'Identifiants ou mot de passe invalides.';
            $this->addFlash('error', $errorMessage);

        } catch (\Exception $e) {
            // Gestion des erreurs de connexion/réseau (ex: serveur indisponible)
            $this->addFlash('error', 'Impossible de contacter le serveur d\'authentification : ' . $e->getMessage());
        }

        return $this->render('api-security/login.html.twig');
    }
}


