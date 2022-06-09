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
    public function index(ConferenceRepository $ConferenceRepository, Request $request): Response
    {
        $city_search = $request->query->get('city_search', '');
        $year_search = $request->query->get('year_search', '');
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $ConferenceRepository->getConferencePaginator($offset, $year_search, $city_search);
        $years = $ConferenceRepository->getListYear();
        $cities = $ConferenceRepository->getListCity();
        return $this->render(
            'conference/index.html.twig',
            [
                'conferences' => $paginator,
                'previous' => $offset - ConferenceRepository::PAGINATOR_PER_PAGE_CONF,
                'next' => min(count($paginator), $offset + ConferenceRepository::PAGINATOR_PER_PAGE_CONF),
                'years' => $years,
                'year_search' => $year_search,
                'cities' => $cities,
                'city_search' => $city_search
            ]
        );
    }





    #[Route('/conference/{id}', name: 'ficheConference')]
    public function show(Conference $conference, CommentRepository $commentRepository, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);
        $nbrePages = ceil(count($paginator) / CommentRepository::PAGINATOR_PER_PAGE);
        $next = min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE);
        $pageActuelle = ceil($next / CommentRepository::PAGINATOR_PER_PAGE);
        $difPages = $nbrePages - $pageActuelle;

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'next' => $next,
            'nbrePages' => $nbrePages,
            'offset' => CommentRepository::PAGINATOR_PER_PAGE,
            'pageActuelle' => $pageActuelle,
            'difPages' => $difPages,
            // 'dir' => $photoUrl
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
