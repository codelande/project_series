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

    #[Route('/{program}/', methods: ['GET'], name: 'show')]
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
    #[Route('/{programId}/seasons/{seasonId}/', methods: ['GET'], name: 'season_show')]
    public function showSeason(Program $programId, Season $seasonId, ProgramRepository $programRepository, SeasonRepository $seasonRepository, EpisodeRepository $episodeRepository)
    {
        $episodes = $episodeRepository->findBy(['season' => $seasonId->getId()]);
        return $this->render('program/season_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episodes' => $episodes,
        ]);
    }

    #[Route('/{programId}/season/{seasonId}/episode/{episodeId}', methods: ['GET'], name: 'episode_show')]
    public function showEpisode(Program $programId, Season $seasonId, Episode $episodeId)
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episode' => $episodeId,
        ]);
    }
}
