<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

session_unset();
session_destroy();

session_start();
setFlash('success', 'You have been logged out successfully.');

header("Location: " . BASE_URL . "/login.php");
exit();
