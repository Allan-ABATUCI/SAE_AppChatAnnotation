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
            $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

            if ($bd->UserExists($email, $mdp)) {
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $bd->getUser($email, $mdp)['user_id'];
                

                $this->render('contact');
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
            $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
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
