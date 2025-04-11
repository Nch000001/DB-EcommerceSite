<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLevel(int $requiredLevel) {
    if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < $requiredLevel) {
        header("Location: /ecommerce/index.php");
        exit;
    }
}