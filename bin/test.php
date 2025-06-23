<?php
ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', 'localhost:11211');

$m = new \Memcached();
$m->addServer('localhost', 11211);

// Options de debug
$m->setOption(\Memcached::OPT_BINARY_PROTOCOL, false);
$m->setOption(\Memcached::OPT_COMPRESSION, false);

session_start();
$_SESSION['test'] = ['a' => 1, 'b' => 2];
session_write_close();

$sessionId = session_id();
$keys = $m->getAllKeys();

echo "Clés disponibles:\n";
print_r($keys);

$prefixes = ['memc.sess.key.', 'memc.sess.', 'sess_'];
foreach ($prefixes as $prefix) {
    $key = $prefix . $sessionId;
    $data = $m->get($key);
    
    echo "\nEssai avec clé: $key\n";
    echo "Données brutes:\n";
    var_dump($data);
    
    if ($data !== false) {
        echo "Tentative unserialize():\n";
        $unserialized = @unserialize($data);
        var_dump($unserialized);
        
        echo "Tentative session_decode():\n";
        $_SESSION = [];
        $result = @session_decode($data);
        var_dump($result, $_SESSION);
        
        break;
    }
}
$m->set('test_key', serialize(['a'=>1]));
var_dump(unserialize($m->get('test_key')));