<?php
namespace MVC\controller;

use PDO;
use PDOException;

class SearchController {
    private $pdo;

    public function __construct() {
        // Initialize database connection
        $dsn = 'mysql:host=127.0.0.1;dbname=wise';
        $user = 'root';
        $pass = '994422Gg';
        $option = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'];
        
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $option);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function index() {
        try {
            $users = $this->search($_GET);
            
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

    private function search($queryParams) {
        try {
            // Only show employees (AdminID = 2)
            $sql = "SELECT * FROM user WHERE AdminID = 2";
            $params = [];
            
            // Search term
            if (!empty($queryParams['q'])) {
                $searchTerm = "%" . $queryParams['q'] . "%";
                $sql .= " AND (User_Name LIKE ? OR User_Email LIKE ? OR User_ID = ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                
                // Check if search term is numeric for ID search
                if (is_numeric($queryParams['q'])) {
                    $params[] = (int)$queryParams['q'];
                } else {
                    $params[] = -1; // Will not match any ID if search term is not numeric
                }
            }
            
            // Sorting
            $sort = $queryParams['sort'] ?? 'date_desc';
            switch ($sort) {
                case 'name_asc':
                    $sql .= " ORDER BY User_Name ASC";
                    break;
                case 'name_desc':
                    $sql .= " ORDER BY User_Name DESC";
                    break;
                case 'date_asc':
                    $sql .= " ORDER BY last_login ASC";
                    break;
                case 'date_desc':
                default:
                    $sql .= " ORDER BY last_login DESC";
                    break;
            }
            
            // Execute query
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $users;
            
        } catch (PDOException $e) {
            // Log error
            error_log("Search Error: " . $e->getMessage());
            return [];
        }
    }
}
