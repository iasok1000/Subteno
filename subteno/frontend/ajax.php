<?php
if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    http_response_code(410);
    exit();
}
session_start();
require $_SERVER["DOCUMENT_ROOT"] . '/subteno/backend/config.php';
require $_SERVER["DOCUMENT_ROOT"] . '/subteno/backend/message.php';
