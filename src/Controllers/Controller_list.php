<?php
namespace App\Controllers;
use App\Controllers\Controller;
use App\Models\Model;

class Controller_list extends Controller
{
    function action_default(): void
    {
        $this->action_contact();
    }
    function action_contact()
    {
       // session_start();
        if ($_SESSION['id']) {
            $bd = Model::getModel();
            $cont = $bd->getOnlineUsers();
            
            $data = [
                'contacts' => $cont,
            ];
            $this->render("contact", $data);

        } else {
            $this->render("login");
        }
    }
    

}