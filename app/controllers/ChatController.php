<?php

use LaravelRealtimeChat\Repositories\Conversation\ConversationRepository;
use LaravelRealtimeChat\Repositories\User\UserRepository;
    
class ChatController extends \BaseController {

    /**
     * @var LaravelRealtimeChat\Repositories\ConversationRepository
     */
    private $conversationRepository; 

    /**
     * @var LaravelRealtimeChat\Repositories\UserRepository
     */
    private $userRepository; 

    public function __construct(ConversationRepository $conversationRepository, UserRepository $userRepository)
    {
        $this->conversationRepository = $conversationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display the chat index.
     *
     * @return Response
     */
    public function conversation() {
        $current_conversation = false;
        if(Input::has('conversation')) {
            $current_conversation = $this->conversationRepository->getByName(Input::get('conversation'));
        } else {
            $current_conversation = Auth::user()->conversations()->first();
        }
        if ($current_conversation) {
            Session::set('current_conversation', $current_conversation->name);
            foreach($current_conversation->messages_notifications as $notification) {
                $notification->read = true;
                $notification->save();
            }
        } else {
            $current_conversation = new stdClass() ;
            $current_conversation->name=null;
        }

        $messages=[];
        $conversations = Auth::user()->conversations->map(function($conversation) use (&$current_conversation)
        {
            $users=$conversation->users->map(function($user){
                return [
                    'image_path'       => $user->image_path,
                    'full_name' => $user->username,
                    'area'      => $user->areas->nombre,
                ];
            });
            $current=false;
            if ($conversation->name==$current_conversation->name){
                $current_conversation->messages=$conversation->messages;
                $current=true;
            }
            return [
                'users'    => $users,
                'messages' => $conversation->messages,
                'name' => $conversation->name,
                'messages_notifications_count'=> $conversation->messages_notifications->count(),
                'last_message'=> Str::words($conversation->messages->last()->body, 5),
                'current'=> $current,
            ];
        });
        $response=[
            'current_conversation'   =>$current_conversation,
            'conversations'   =>$conversations,
            //'messages'   =>$messages,
        ];
        return Response::json($response);
    }

    /**
     * Display the chat index.
     *
     * @return Response
     */
    public function index() {

        $viewData = array();

        if(Input::has('conversation')) {
            $viewData['current_conversation'] = $this->conversationRepository->getByName(Input::get('conversation'));
        } else {
            $viewData['current_conversation'] = Auth::user()->conversations()->first();
        }

        if($viewData['current_conversation']) {
            Session::set('current_conversation', $viewData['current_conversation']->name);
    
            foreach($viewData['current_conversation']->messages_notifications as $notification) {
                $notification->read = true;
                $notification->save();
            }
        }
       
        $users = $this->userRepository->getAllExcept(Auth::user()->id);

        foreach($users as $key => $user) {
            $viewData['recipients'][$user->id] = $user->username;
        }
        
        $viewData['conversations'] = Auth::user()->conversations()->get();
        
        return View::make('templates/chat', $viewData);
    }
}
