<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Form\ConfAddFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{

    //& listing des conférences
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




    //& Affichage d'une conférence
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

        
    
    //& Ajout d'un commentaire à une conférence
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

    //& Ajout d'une conférence
    #[Route('/newconference', name: 'conference_newconference')]
    public function newConference(ConferenceRepository $conferenceRepository, Request $request): Response
    {
        $conf = new Conference();
        $form = $this->createForm(ConfAddFormType::class, $conf);
        // $photoDir = 'uploads/photos';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // if ($photo = $form['photoFileName']->getData()) {
            //     $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
            //     try {
            //         $photo->move($photoDir, $filename);
            //     } catch (FileException $e) {
            //        // unable to upload the photo, give up
            //     }
            //     $conf->setPhotoFilename($filename);
            $conferenceRepository->add($conf, true);
            // }
            return $this->redirectToRoute('app_conference');
        }
        return $this->render('conference/newconference.html.twig', [
            'form_add' => $form->createView(),
            'page' => 'add'
        ]);
    }

    //& Modification d'une conférence
    #[Route('/modifconference/{id}', name: 'conference_modifconference')]
    public function modifConference(Conference $conference, Request $request, ConferenceRepository $conferenceRepository): Response
    {
        $form = $this->createForm(ConfAddFormType::class, $conference);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $conferenceRepository->add($conference, true);
            return $this->redirectToRoute('ficheConference', [ 'id' => $conference->getId()]);
        }
        return $this->render('conference/newconference.html.twig', [
            'form_add' => $form->createView(),
            'page' => 'modif'
        ]);
    }

    //& Suppression d'une conférence
    #[Route('/supprConf/{id}', name: 'conference_supprConf')]
    public function supprConference(ConferenceRepository $conferenceRepository, Conference $conference): RedirectResponse
    {
        $conferenceRepository->remove($conference, true);

        return $this->redirectToRoute('app_conference');
    }
}
