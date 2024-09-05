<?php
if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    http_response_code(410);
    exit();
}
session_start();
require '../backend/config.php';
require '../backend/message.php';
