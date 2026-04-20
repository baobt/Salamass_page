<?php
header('Content-Type: application/json');
echo json_encode([
    'headers' => getallheaders(),
    'method' => $_SERVER['REQUEST_METHOD'],
]);