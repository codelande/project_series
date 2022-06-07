<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramType;
use App\Repository\CommentRepository;
use App\Service\Slugify;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', [

            'programs' => $programs,

        ]);
    }
    #[Route('/new', name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository, Slugify $slugify, MailerInterface $mailer): Response

    {
        $program = new program();

        // Create the form, linked with $program
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $programRepository->add($program, true);
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('test@test.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);
            // Redirect to categories list
            return $this->redirectToRoute('categories_index');
        }

        return $this->renderForm('program/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/', methods: ['GET'], name: 'show')]
    public function show(Program $program, SeasonRepository $seasonRepository)
    {
        // same as $program = $programRepository->find($id);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program . ' found in program\'s table.'
            );
        }

        $seasons = $seasonRepository->findBy(['program' => $program]);

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    #[Route('/{slug}/edit', methods: ['GET', 'POST'], name: 'edit')]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository)
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if (!($this->getUser() == $program->getOwner())) {
            // If notthe owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $programRepository->add($program, true);

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/seasons/{seasonId}/', methods: ['GET'], name: 'season_show')]
    public function showSeason(Program $programId, Season $seasonId, ProgramRepository $programRepository, SeasonRepository $seasonRepository, EpisodeRepository $episodeRepository)
    {
        $episodes = $episodeRepository->findBy(['season' => $seasonId->getId()]);
        return $this->render('program/season_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episodes' => $episodes,
        ]);
    }

    #[Route('/{slug}/season/{seasonId}/episode/{episodeId}', methods: ['GET'], name: 'episode_show')]
    public function showEpisode(CommentRepository $commentRepository, Request $request, Program $programId, Season $seasonId, Episode $episodeId)
    {
        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episodeId);
            $commentRepository->add($comment, true);

            return $this->redirectToRoute('season_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episode' => $episodeId,
            'form' => $form,
        ]);
    }
}
