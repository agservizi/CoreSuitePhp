<?php
// Placeholder per Controller base
namespace App\Controller;
class BaseController {
    // Helper per redirect
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}
