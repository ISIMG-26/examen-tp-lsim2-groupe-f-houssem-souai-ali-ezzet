<?php
// back/logout.php
require_once __DIR__ . '/config.php';
sessionStart();
session_destroy();
redirect('../index.php');
