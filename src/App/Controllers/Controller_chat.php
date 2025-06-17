<?php

namespace App\Controllers;

use App\Models\Model;
use App\Controllers\Controller;

class Controller_chat extends Controller
{
    public function action_default()
    {

        $this->action_chat();
    }

    public function action_chat()
    {
        $this->render('chat');
    }
    public function action_save(): void
    {
        $bd=Model::getModel();
        $msg = $_POST['msg'] ?? false;
        $sender = $_SESSION['id'] ?? false;
        if ($msg && $sender && preg_match('/^\S+$/', $msg)) {
            insertMessageWithEmotion();
        }
    }
} 
