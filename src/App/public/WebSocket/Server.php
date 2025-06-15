<?php

namespace websocket;

use Ratchet\App;
use websocket\Chat;
class Server
{
    public function run()
    {
        include "src/App/Auth/credentials.php";
        $handler = new Chat();
        $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Configure PDO session handler
$sessionHandler = new PdoSessionHandler(
    $pdo,
    [
        'db_table' => 'sessions',           
        'db_id_col' => 'sess_id',           
        'db_data_col' => 'sess_data',       
        'db_lifetime_col' => 'sess_lifetime', 
        'db_time_col' => 'sess_time',       
        'lock_mode' => PdoSessionHandler::LOCK_TRANSACTIONAL 
    ]
);

        $app = new App('localhost', 8081, '0.0.0.0');
        $app->route('/chat', $handler, ['*']);
        $app->run();
    }
}