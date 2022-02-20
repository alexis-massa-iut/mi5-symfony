<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CommandLineRepository;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/{_locale}/user', requirements: ['_locale' => '%app.supported_locales%'], defaults: ['_locale' => 'en'])]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $ur): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $ur->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserRepository $ur, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($ur->findOneBy(['user' => $user])) {
                $this->addFlash('error', 'Email already used');
                return $this->redirect($request->headers->get('referer'));
            }
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword)
                ->setRoles(["ROLE_USER"]);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/account', name: 'user_account', methods: ['GET'])]
    public function account(UserRepository $ur): Response
    {
        $user_id = $this->getUser()->getUserIdentifier();
        return $this->render('user/account.html.twig', [
            'user' => $ur->findOneBy(['email' => $user_id]),
        ]);
    }

    #[Route('/commands', name: 'user_commands', methods: ['GET'])]
    public function commands(CommandRepository $cr, CommandLineRepository $clr, ProductRepository $pr, UserRepository $ur): Response
    {
        $user_email = $this->getUser()->getUserIdentifier(); // Get user email
        $commands = $cr->findBy(['user' => $ur->findOneBy(['email' => $user_email])->getId()]); // Get all commands by user (user by email)
        return $this->render('command/index.html.twig', ['commands' => $commands]);
    }

    #[Route('/command/{id}', name: 'user_command', methods: ['GET'])]
    public function command(CommandRepository $cr, int $id): Response
    {
        $command = $cr->findOneBy(['id' => $id]); // command
        return $this->render('command/show.html.twig', ['command' => $command,]);
    }
}
