<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;


#[Route('/categories', name: 'categories_')]

class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $CategoryRepository): Response
    {
        $categories = $CategoryRepository->findAll();

        return $this->render('category/index.html.twig', [

            'categories' => $categories,

        ]);
    }

    #[Route('/{categoryName}/', methods: ['GET'], name: 'show')]
    public function show(CategoryRepository $CategoryRepository, ProgramRepository $programRepository, $categoryName = 'default')
    {

        $category = $CategoryRepository->findOneBy(['name' => $categoryName]);
        $programs = $programRepository->findBy(['category' => $category->getId()], array('title' => 'ASC'), 3);

        return $this->render('category/show.html.twig', [
            'programs' => $programs,
        ]);
    }
}
