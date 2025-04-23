<?php

class BaseController
{
    protected function render($view, $data = [])
    {
        // Extract the $data array to variables
        extract($data);

        // Convert dot notation like 'auth.login' to file path
        $viewPath = str_replace('.', '/', $view);

        // Full path to the views
        $fullPath = '../app/Views/' . $viewPath . '.php';

        // Check if file exists
        if (file_exists($fullPath)) {
            require $fullPath;
        } else {
            echo "View not found: $fullPath";
        }
    }
}
