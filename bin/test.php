<?php
// Configure Memcached as session handler
ini_set('session.save_handler', 'memcached');

// For Memcached, use this format for save_path:
// "host:port,host2:port2?timeout=1&retry_interval=15"
ini_set('session.save_path', 'localhost:11211');

// Initialize Memcached client
$m = new \Memcached();
$m->addServer('localhost', 11211);

// Set custom session ID prefix (optional)
ini_set('memcached.sess_prefix', 'memc.sess.key.');

session_start();

// Store test value
$_SESSION['test'] = 'value';
session_write_close(); // Ensure session is saved

// Debug output
echo "Current session ID: " . session_id() . "\n";
echo "Session data from \$_SESSION:\n";
var_dump($_SESSION);

// Verify directly in Memcached
$memcachedKey = 'memc.sess.key.' . session_id();
echo "\nMemcached data for key '$memcachedKey':\n";
var_dump($m->get($memcachedKey));

// Alternative: get all keys (may not work in all setups)
echo "\nAll Memcached keys (may be limited):\n";
var_dump($m->getAllKeys());