<?php

namespace src\handlers;

use \src\models\User;
use \src\models\UserRelation;
use \src\handlers\PostHandler;

class UserHandler 
{
    public static function checkLogin()
     {
        if(!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if(count($data) > 0) {
                $loggedUser = new User();

                $loggedUser->id = $data['id'];                
                $loggedUser->name = $data['name'];
                $loggedUser->avatar = $data['avatar'];
                                
                return $loggedUser;
            } 
        }

        return false;
     }

     public function verifyLogin($email, $password)
     {
         $user = User::select()->where('email', $email)->one();

         if($user) {
             if(password_verify($password, $user['password'])) {
                $token = md5(time().rand(0,9999).time());
                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                ->execute();

                return $token;
             }
         }

         return false;
     }

     public function idlExists($id) 
     {
        $user = User::select()->where('id', $id)->one();
        return $user ? true : false;
     }

     public function emailExists($email) 
     {
        $user = User::select()->where('email', $email)->one();
        return $user ? true : false;
     }

     public function getUser($id, $full = false)
     {
         $data = User::select()->where('id', $id)->one();

         if($data) {
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->avatar = $data['avatar'];
            $user->work = $data['work'];
            $user->cover = $data['cover'];

            if($full) {
                $user->followers = [];
                $user->following = [];
                $user->photos = [];
                
                // followers - Pessoas as que segue x usuario.
                $followers = UserRelation::select()->where('user_to', $id)->get();
                foreach($followers as $follower) {
                    $userData = User::select()->where('id', $follower['user_from'])->one();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;
                }

                // following - quem é que eu sigo.
                $following = UserRelation::select()->where('user_from', $id)->get();
                foreach($following as $follower) {
                    $userData = User::select()->where('id', $follower['user_to'])->one();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;
                }


                // photos                
                $user->photos = PostHandler::getPhotosFrom($id);
            }
            
            return $user;
         }

         return false;
     }

     public function addUser($name, $email, $password, $birthdate)
     {
         $hash = password_hash($password, PASSWORD_DEFAULT);
         $token = md5(time().rand(0,9999).time());

         User::insert([
             'email' => $email,
             'password' => $hash,
             'name' => $name,
             'birthdate' => $birthdate,             
             'token' => $token
         ])->execute();

         return $token;
     }
}