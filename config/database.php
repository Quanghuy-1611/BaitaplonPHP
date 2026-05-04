<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'ql_nhansu_luong';

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    header('Location: /BaitaplonPHP/setup.php');
    exit;
}

$conn->set_charset('utf8mb4');
