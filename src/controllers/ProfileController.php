<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller 
{
    private $loggedUser;

    public function __construct() 
    {
        $this->loggedUser = UserHandler::checkLogin();        
        if(UserHandler::checkLogin() === false)
        {
            $this->redirect('/login');
        }
    }

    public function index($atts = []) 
    { 
        $page = intval(filter_input(INPUT_GET, 'page'));
        
        // DETECTA O USUÁRIO ACESSADO
        $id = $this->loggedUser->id;        
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }
        //PESQUISA O USUÁRIO
        $user = UserHandler::getUser($id, true);

        if(!$user) {
            $this->redirect('/');
        }

        //APRESENTA A IDADE
        $dateFrom = new \Datetime($user->birthdate);
        $dateTo = new \DateTime('today');        
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        // PESQUISA O FEED DO USUÁRIO
        $feed = PostHandler::getUserFeed($id, $page, $this->loggedUser->id);

        // VERIFICA SE EU SIGO O USUÁRIO
        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);            
        }

        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
    }

    public function follow($atts)
    {
        // Quem eu vou seguir
        $to = intval($atts['id']);
        
        if(UserHandler::idExists($to)) {

            if(UserHandler::isFollowing($this->loggedUser->id, $to)) {
                // Não seguir
                UserHandler::unfollow($this->loggedUser->id, $to);
            } else {
                // seguir
                UserHandler::follow($this->loggedUser->id, $to);
            }

        }
        
        $this->redirect('/perfil/'.$to);

    }

    public function friends($atts = []) {

        // DETECTA O USUÁRIO ACESSADO
        $id = $this->loggedUser->id;        
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }
        //PESQUISA O USUÁRIO
        $user = UserHandler::getUser($id, true);

        if(!$user) {
            $this->redirect('/');
        }

        // VERIFICA SE EU SIGO O USUÁRIO
        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);            
        }

        $this->render('profile_friends', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,            
            'isFollowing' => $isFollowing
        ]);
    }

    public function photos($atts = []) {
        // DETECTA O USUÁRIO ACESSADO
        $id = $this->loggedUser->id;        
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }
        //PESQUISA O USUÁRIO
        $user = UserHandler::getUser($id, true);

        if(!$user) {
            $this->redirect('/');
        }

        // VERIFICA SE EU SIGO O USUÁRIO
        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);            
        }

        $this->render('profile_photos', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,            
            'isFollowing' => $isFollowing
        ]);
    }
}