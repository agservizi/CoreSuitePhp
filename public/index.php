<?php
// Entry point principale CoreSuite
require_once __DIR__ . '/../core/Router.php';
$core = new Core\Router();
$core->run();
