<?php

namespace App\Controller;

use GuzzleHttp\Psr7\Request;
use OpenAI;
use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    #[Route('/', name: 'app_chat')]
    public function index(HttpFoundationRequest $request): Response
    {
        $chatRequest = (empty($request->request->get('userRqChat'))) ? "Give a short introduction to your functionalities" : $request->request->get('userRqChat');
        //dd($chatRequest);

        $token = $this->getParameter('app.gpttoken');
        $client = OpenAI::client($token);

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $chatRequest],
            ],
        ]);

        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController'
            , 'answer' => $result->choices[0]->message->content
            , 'answerFrom' => $chatRequest
        ]);
    }
}