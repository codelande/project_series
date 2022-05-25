<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Form\CategoryType;
use App\Entity\Category;



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

    #[Route('/new', name: 'new')]
    public function new(): Response

    {
        $category = new Category();

        // Create the form, linked with $category
        $form = $this->createForm(CategoryType::class, $category);

        // Render the form (best practice)
        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);

        // Alternative
        // return $this->render('category/new.html.twig', [
        //   'form' => $form->createView(),
        // ]);
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
