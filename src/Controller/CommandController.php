<?php

namespace App\Controller;

use App\Entity\Command;
use App\Form\CommandType;
use App\Repository\CommandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale}/user/command', requirements: ['_locale' => '%app.supported_locales%'])]
class CommandController extends AbstractController
{
    #[Route('/', name: 'command_index', methods: ['GET'])]
    public function index(CommandRepository $commandRepository): Response
    {
        return $this->render('command/index.html.twig', [
            'commands' => $commandRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'command_show', methods: ['GET'])]
    public function show(Command $command): Response
    {
        return $this->render('command/show.html.twig', [
            'command' => $command,
        ]);
    }

}
