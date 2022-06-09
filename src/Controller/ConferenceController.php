<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    #[Route('/', name: 'app_conference')]
    // je liste toute les conferences avec un paginator
    public function index(ConferenceRepository $conferenceRepository, Request $request): Response
    {
        $cities = $conferenceRepository->getListCity();
        $cities_search = $request->query->get('cities_search', '');
        
        $years = $conferenceRepository->getListYear();
        $year_search = $request->query->get('year_search', '');
        
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $conferenceRepository ->getConferencePaginator($offset, $year_search, $cities_search);
        $years = $conferenceRepository->getListYear();
        $cities = $conferenceRepository->getListCity();
        return $this->render('conference/index.html.twig', [
            'cities_search' => $cities_search,
            'cities' => $cities,
            'year_search' => $year_search,
            'years' => $years,
            'previous'=> $offset - ConferenceRepository::PAGINATOR_PER_PAGE_CONF,
            'next'=> min(count($paginator), $offset + ConferenceRepository::PAGINATOR_PER_PAGE_CONF),
            'conferences' => $paginator,
        ]);
    }
            
        
    







    // public function index(ConferenceRepository $conferenceRepository): Response
    // {
    // return new Response('<h1>ok</h1>');
    // appelle le template et lui passe un tableau nommé « conferences » qui contient un tableau de string renseignée en dur : $conferences = ["Paris (2022)", "Montpellier (2021)"];
    // $conferences = ["Paris (2022)", "Montpellier (2021)"];
    // return $this->render('conference/index.html.twig', ['conferences' => $conference]);
    //     return $this->render('conference/index.html.twig', [
    //         'conferences' => $conferenceRepository->findAll(),
    //     ]);
    // }





    #[Route('/conference/{id}', name: 'ficheConference')]
    public function show(Conference $conference, CommentRepository $commentRepo, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepo->getCommentPaginator($conference, $offset);

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]);
    }

        
    // public function ficheConference(ConferenceRepository $conferenceRepository, $id): Response
    // {
    //     $conference = $conferenceRepository->find($id);
    //     return $this->render('conference/show.html.twig', [
    //         'conference' => $conference,
    //         'comments' => $conference->getComments(Conference::class, [
    //             'Auteur'=> 'author',
    //             'Contenu'=>'text',
    //             'E-mail'=>'email',
    //             'Date'=>'createdAt',
    //             'Note'=>'note',
    //         ]),
    //     ]);
    // }

    #[Route('/conference/{id}/newComment', name: 'conference_newcomment')]
            public function newComment(Conference $conference, Request $request, ManagerRegistry $doctrine, CommentRepository $commentRepository): Response
            {
                $comment = new Comment();
                $form = $this->createForm(CommentFormType::class, $comment);

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $comment->setConference($conference);
                    $comment->setCreatedAt(new \DateTimeImmutable());
                    
                    $commentRepository->add($comment, true);

                    // ou en utilisant le manager
                    // $manager = $doctrine->getManager();
                    // $manager->persist($comment);
                    // $manager->flush();
                
                    return $this->redirectToRoute('ficheConference', [ 'id' => $conference->getId()]);
                }

                return $this->render('conference/newcomment.html.twig', [
                    'conference' => $conference,
                    'form_comment' => $form->createView()
                    ]);
            }
}
