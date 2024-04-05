<?php

namespace App\Controller;

use App\Entity\ChatSession;
use App\Entity\ChatVersion;
use App\Entity\History;
use App\Repository\ChatSessionRepository;
use App\Repository\ChatVersionRepository;
use App\Repository\HistoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Request;
use OpenAI;
use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class ChatController extends AbstractController
{
    #[Route('/', name: 'app_chat')]
    public function index(HttpFoundationRequest $request, HistoryRepository $historyRepository, ChatVersionRepository $chatVersionRepository, ChatSessionRepository $chatSessionRepository, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $session->start();
        $idSession=$session->get('idSession');
        $ts = new DateTime();
        $ts->modify('+2 hour');

        //Configurer chatGPT
        $token = $this->getParameter('gpttoken');
        $client = OpenAI::client($token);

        $allMessagesSession = $historyRepository->findAllFromSession($session);
        $messagesOpenAI = array();
        $chatVersion = $chatVersionRepository->find(1); 
        $prompt = (empty($request->request->get('userRqChat'))) ? ((empty($allMessagesSession)) ? "Give a short introduction to your functionalities" : false) : $request->request->get('userRqChat');
        
        //Initialisation de $chatSession
        if(empty($idSession)){
            $chatSession = new ChatSession();
            $chatSession->setTs($ts);
            $em->persist($chatSession);
            $em->flush();
            $idSession = $chatSession->getId();
            $session->set('idSession', $idSession);
        }
        else{
            $chatSession = $chatSessionRepository->find($idSession);
            
            foreach ($allMessagesSession as $msg){
                $messagesOpenAI[] = ["role" => "user", "content" => $msg->getPrompt()];
                $messagesOpenAI[] = ["role" => "assistant", "content" => $msg->getMessage()];
            }
                
        }
        if($prompt){
            $messagesOpenAI[] = ["role" => "assistant", "content" => $prompt];

            //Utiliser chatGPT
            $result = $client->chat()->create([
                'model' => $chatVersion->getLabel()
                , 'messages' => $messagesOpenAI
            ]);
            $answer = $result->choices[0]->message->content;
            $messagesOpenAI[] = ["role" => "assistant", "content" => $answer];

            //Stocker la réponse en BDD
            if(!empty($answer)){
                $history = new History();
                $history->setMessage($answer);
                $history->setPrompt($prompt);
                $history->setTs($ts);
                $history->setSession($chatSession);
                $history->setVersion($chatVersion);

                $em->persist($history);
                $em->flush();
            }
        }
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController'
            , 'messages' => $messagesOpenAI
        ]);
    }

    #[Route('/ajax', name: 'app_chat')]
    public function indexAjax(HttpFoundationRequest $request, HistoryRepository $historyRepository, ChatVersionRepository $chatVersionRepository, ChatSessionRepository $chatSessionRepository, EntityManagerInterface $em): Response
    {
        
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController'
        ]);
    }
}