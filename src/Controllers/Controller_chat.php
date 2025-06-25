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
        if (!isset($_SESSION['id']) || session_status() === PHP_SESSION_NONE || !preg_match('/^\d+$/', $_SESSION['id'])) {
        header("Location: ?controller=login");
    }   
    $bd=Model::getModel();
    $this->render("chat",$bd->getUserById(e($_GET['id'])));
    }


    /* 
    public function action_react(): void
    {
        $bd = Model::getModel();
        $input = json_decode(file_get_contents('php://input'), true);
        $messageId = $input['messageId'] ?? null;
        $emoji = $input['emoji'] ?? null;
        $userId = $_SESSION['id'] ?? null;

        if ($messageId && $emoji && $userId) {
            $bd->addReactionToMessage(userId: $messageId, $messageId, $emoji);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        }
    } */
} 
