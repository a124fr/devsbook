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
        $id = $this->loggedUser->id;
        
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        } 

        //PESQUISA O USUÁRIO
        

        $this->render('profile', [
            'loggedUser' => $this->loggedUser
        ]);
    }
    
}