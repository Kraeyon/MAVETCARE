<?php

namespace App\Controllers;

class BaseController {

    protected function getViewPath(string $relativePath): string {
        $path = __DIR__ . "/../Views/{$relativePath}.php";

        if (!file_exists($path)) {
           echo "View not found: {$relativePath}";
        }

        return $path;
    }
    
    public function render($view, $data = []) {
        // Start output buffering only once
        ob_start();

        $viewPath = $this->getViewPath($view);

        extract($data);

        include $viewPath;

        $content = ob_get_clean();

        echo $content;
    }
}