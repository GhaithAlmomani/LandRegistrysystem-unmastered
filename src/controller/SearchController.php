<?php
namespace MVC\controller;

require_once __DIR__ . '/../../config/database.php';

use MVC\model\User;
use Exception;

class SearchController {
    public function index() {
        try {
            $q = $_GET['q'] ?? $_POST['search_box'] ?? $_POST['q'] ?? null;
            $sort = $_GET['sort'] ?? $_POST['sort'] ?? 'date_desc';
            $users = User::searchEmployees($q, $sort);
            
            // Start output buffering
            ob_start();
            
            // Include the search view
            require __DIR__ . '/../view/home/Admin/search.tpl.php';
            
            // Get the content and clean the buffer
            $content = ob_get_clean();
            
            // Include the main layout
            require __DIR__ . '/../view/layouts/main.tpl.php';
            
        } catch (Exception $e) {
            // Handle error
            error_log("Search Error: " . $e->getMessage());
            echo "An error occurred while performing the search. Please try again later.";
        }
    }
}
