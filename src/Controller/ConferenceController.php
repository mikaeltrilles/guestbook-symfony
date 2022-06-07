<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    #[Route('/conference', name: 'app_conference')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        // return new Response('<h1>ok</h1>');
        // appelle le template et lui passe un tableau nommé « conferences » qui contient un tableau de string renseignée en dur : $conferences = ["Paris (2022)", "Montpellier (2021)"];
        // $conferences = ["Paris (2022)", "Montpellier (2021)"];
        // return $this->render('conference/index.html.twig', ['conferences' => $conference]);
        return $this->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]);
    }
    #[Route('/conference/{id}', name: 'ficheConference')]
        public function ficheConference(ConferenceRepository $conferenceRepository, $id): Response
        {
            $conference = $conferenceRepository->find($id);
            return $this->render('conference/ficheConference.html.twig', [
                'conference' => $conference,
                'comments' => $conference->getComments(Conference::class, [
                    'Auteur'=> 'author',
                    'Contenu'=>'text',
                    'E-mail'=>'email',
                    'Date'=>'createdAt',
                    'Note'=>'note',
                ]),
            ]);
        }
}
