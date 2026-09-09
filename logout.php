<?php
require_once __DIR__ . '/config.php';
logoutUser();
header('Location: index.php');
exit;
