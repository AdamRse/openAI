<?php

namespace App\Controller;

use App\Entity\ChatSession;
use App\Form\ChatSessionType;
use App\Repository\ChatSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chat/session')]
class ChatSessionController extends AbstractController
{
    #[Route('/', name: 'app_chat_session_index', methods: ['GET'])]
    public function index(ChatSessionRepository $chatSessionRepository): Response
    {
        return $this->render('chat_session/index.html.twig', [
            'chat_sessions' => $chatSessionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chat_session_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chatSession = new ChatSession();
        $form = $this->createForm(ChatSessionType::class, $chatSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chatSession);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat_session/new.html.twig', [
            'chat_session' => $chatSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_session_show', methods: ['GET'])]
    public function show(ChatSession $chatSession): Response
    {
        return $this->render('chat_session/show.html.twig', [
            'chat_session' => $chatSession,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chat_session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChatSession $chatSession, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatSessionType::class, $chatSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat_session/edit.html.twig', [
            'chat_session' => $chatSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_session_delete', methods: ['POST'])]
    public function delete(Request $request, ChatSession $chatSession, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chatSession->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($chatSession);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_session_index', [], Response::HTTP_SEE_OTHER);
    }
}
