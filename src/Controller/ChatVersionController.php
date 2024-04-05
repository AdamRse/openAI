<?php

namespace App\Controller;

use App\Entity\ChatVersion;
use App\Form\ChatVersionType;
use App\Repository\ChatVersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chat/version')]
class ChatVersionController extends AbstractController
{
    #[Route('/', name: 'app_chat_version_index', methods: ['GET'])]
    public function index(ChatVersionRepository $chatVersionRepository): Response
    {
        return $this->render('chat_version/index.html.twig', [
            'chat_versions' => $chatVersionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chat_version_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chatVersion = new ChatVersion();
        $form = $this->createForm(ChatVersionType::class, $chatVersion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chatVersion);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_version_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat_version/new.html.twig', [
            'chat_version' => $chatVersion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_version_show', methods: ['GET'])]
    public function show(ChatVersion $chatVersion): Response
    {
        return $this->render('chat_version/show.html.twig', [
            'chat_version' => $chatVersion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chat_version_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChatVersion $chatVersion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatVersionType::class, $chatVersion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_version_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat_version/edit.html.twig', [
            'chat_version' => $chatVersion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_version_delete', methods: ['POST'])]
    public function delete(Request $request, ChatVersion $chatVersion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chatVersion->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($chatVersion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_version_index', [], Response::HTTP_SEE_OTHER);
    }
}
