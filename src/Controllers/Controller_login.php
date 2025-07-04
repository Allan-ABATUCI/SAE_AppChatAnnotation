<?php

namespace App\Controllers;

use App\Models\Model;

class Controller_login extends Controller
{

    public function action_default()
    {
        $this->action_form_login();
    }
    public function action_form_login()
    {

        $this->render('login');
    }
public function action_login()
{
    if (isset($_POST['submit_login'])) { 
        $bd = Model::getModel();
        $email = htmlspecialchars($_POST['email']);
        $mdp = htmlspecialchars($_POST['mdp']); 
        $user = $bd->UserExists($email) ? $bd->getUser($email) : false;
        
        if ($user && password_verify($mdp, $user['password_hash'])) {
            // Standard PHP session for web requests
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $user['user_id'];
            session_write_close();
            // Additional shared data in Memcached
            $memcached = new \Memcached();
            $memcached->addServer('localhost', 11211);
            
            // Store WebSocket-relevant data
            $wsData = [
                'user_id' => $user['user_id'],
                'email' => $email,
            ];
            
            $memcached->set('ws_user_'.session_id(), $wsData, 86400); //en secondes
                        
            header('Location: ?controller=list');

        }else{
            
            $error_message = "mot de passe ou email erroné";
        if (!empty($error_message)) {
            echo '<div id="error-msg" style="
                position:absolute;
                padding: 10px;
                background: #ffcccc;
                border: 1px solid red;
                margin: 10px;
                animation: fadeOut 2s forwards;
                animation-delay: 2s;
            ">' . $error_message . '</div>';
            $error_message =false;
            $this->action_form_login();
        
        }
        }
    }
}
    public function action_form_register()
    {
        $this->render('register');
    }
    public function action_register()
    {
        if (isset($_POST['submit_registration'])) {
            $username = htmlspecialchars($_POST['prenom']) . ' ' . htmlspecialchars($_POST['nom']);
            $email = htmlspecialchars($_POST['email']);
            if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                echo "email invalide";
            }
            $mdp = password_hash(htmlspecialchars($_POST['mdp']), PASSWORD_DEFAULT);
            $bd = Model::getModel();

            if (!$bd->UserExists($email)) {
                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = $bd->createUser($username, $email, $mdp);
                $wsData = [
                    'user_id' => $_SESSION['user_id'],
                    'email' => $_SESSION['email'],
                ];

                $memcached = new \Memcached();
                $memcached->addServer('localhost', 11211);
                $memcached->set('ws_user_'.$_SESSION['user_id'], $wsData, 86400);
                
                $bd->updateUserStatus($_SESSION['id'], true);
                header('Location: ?controller=list');
            } else {
                $message = "L'utilisateur existe déjà";
                echo $message;
                $this->render('login');
            }
        }
    }
}
