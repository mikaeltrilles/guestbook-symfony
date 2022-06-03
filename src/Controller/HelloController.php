<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    // #[Route('/', name: 'app_hello')]
    #[Route('/hello/{name} ', name: 'app_hello')]

    public function index(String $name): Response
    {
        // $nom = $request->get('nom');
        return new Response('<html> 
        <body>
        <h1>Bonjour ' . $name . ' !</h1>
        <img src="/images/under-construction.gif" /> 
        </body>
        </html>');
        // return $this->render('hello/index.html.twig', [
        //     'controller_name' => 'HelloController',
        // ]);
    }
}
