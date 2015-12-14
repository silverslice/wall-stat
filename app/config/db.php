<?php

if (file_exists(__DIR__ . '/db-local.php')) {
    return include __DIR__ . '/db-local.php';
}

return [
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname'   => 'vk-wall',
    'charset'  => 'utf8'
];