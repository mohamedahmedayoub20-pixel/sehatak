<?php
include "includes/auth.php";

$language = "en";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

session_destroy();
header("Location: index.php?lang=" . ($language ?? 'en'));
exit();
