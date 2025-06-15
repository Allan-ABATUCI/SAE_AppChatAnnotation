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
    public function action_message()
    {
        $msg = $_POST['msg'] ?? '';
        $sender = $_SESSION['id'] ?? '';

        if (preg_match('/^\S+$/', $msg)) {
        }
    }
}
