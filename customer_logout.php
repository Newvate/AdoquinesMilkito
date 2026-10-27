<?php session_start(); unset($_SESSION['user']); unset($_SESSION['milkito_cart']); header('Location: index.php'); exit;
