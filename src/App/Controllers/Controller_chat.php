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
        $id__utilisateur=e($_GET["id"]);
        $this->render('chat');
    }
    public function action_save(): void
    {
        $bd=Model::getModel();
        $msg = $_POST['msg'] ?? false;
        $sender = $_SESSION['id'] ?? false;
        if ($msg && $sender && preg_match('/^\S+$/', $msg)) {
            //insertMessageWithEmotion();
        }
    }
    
    public function action_react(): void
    {
        $bd = Model::getModel();
        $input = json_decode(file_get_contents('php://input'), true);
        $messageId = $input['messageId'] ?? null;
        $emoji = $input['emoji'] ?? null;
        $userId = $_SESSION['id'] ?? null;

        if ($messageId && $emoji && $userId) {
            $bd->addReactionToMessage($messageId, $userId, $emoji);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        }
    }
} 
