<?php
if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    http_response_code(410);
    exit();
}
session_start();
require PATH_BACKEND . '/config.php';
require PATH_BACKEND . '/message.php';
