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
        if ($_SESSION['id']) {
            $bd = Model::getModel();
            $last = [];
            $cont = $bd->getOnlineUsers();
            foreach ($cont as $c => $row) {
                $last[] = $bd->getLastMesage($_SESSION['id'], $row['sender_id']);
            }
            $data = [
                'contacts' => $cont,
                'lastm' => $last,
            ];
            $this->render("contact", $data);

        } else {
            //dire l'erreur et reco #flm
        }
    }

}