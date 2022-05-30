<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProgramType;
use App\Service\Slugify;
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
    public function showEpisode(Program $programId, Season $seasonId, Episode $episodeId)
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episode' => $episodeId,
        ]);
    }
}
