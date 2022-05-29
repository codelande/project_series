<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Actor;


class ActorController extends AbstractController
{
    #[Route('/actor/{id}', name: 'app_actor')]
    public function show(Actor $actor): Response
    {
        return $this->render('actor/index.html.twig', [
            'actor' => $actor,
        ]);
    }
}
