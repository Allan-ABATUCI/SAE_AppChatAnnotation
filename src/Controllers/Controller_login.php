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
        $email = e($_POST['email']);
        $mdp = e($_POST['mdp']); 
        $user=($bd->UserExists($email))?$bd->getUser($email):false;
        
        if ($user && password_verify($mdp, $user['password_hash'])) {
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['user']=$user;

            header('Location: ?controller=list');
            session_write_close();// pour que la session se sauvegarde, on peux plus écrire mais toujours lire.
            exit();
        } else {
            $message = "Mauvais identifiant ou mot de passe";
            echo $message;
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
            $username = e($_POST['prenom']) . ' ' . e($_POST['nom']);
            $email = e($_POST['email']);
            if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                echo "email invalide";
            }
            $mdp = password_hash(e($_POST['mdp']), PASSWORD_DEFAULT);
            $bd = Model::getModel();

            if (!$bd->UserExists($email)) {
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $bd->createUser($username, $email, $mdp);
                header('Location: ?controller=list');
                $bd->updateUserStatus($_SESSION['id'], true);
            } else {
                $message = "L'utilisateur existe déjà";
                echo $message;
                $this->render('login');
            }
        }
    }
}
