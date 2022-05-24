<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;

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

    #[Route('/{id<\d+>}/', methods: ['GET'], name: 'show')]
    public function show(ProgramRepository $programRepository, SeasonRepository $seasonRepository, $id = 1 )
    {
        $program = $programRepository->findOneBy(['id' => $id]);
        // same as $program = $programRepository->find($id);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $id . ' found in program\'s table.'
            );
        }

        $seasons = $seasonRepository->findBy(['program' => $id]);

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }
    #[Route('/{programId}/seasons/{seasonId}/', methods: ['GET'], name: 'program_season_show')]
    public function showSeason(int $programId, int $seasonId, ProgramRepository $programRepository, SeasonRepository $seasonRepository, EpisodeRepository $episodeRepository)
    {
        $program = $programRepository->findOneBy(['id' => $programId]);
        $season = $seasonRepository->findOneBy(['id' => $seasonId]);
        $episodes = $episodeRepository->findBy(['season' => $season->getId()]);
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }
}
