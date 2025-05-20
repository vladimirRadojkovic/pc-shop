<?php
require_once 'config/config.php';

class ProductController {
    public function showAddForm() {
        $success = false;
        $error = false;
        require 'views/admin/add_product.php';
    }

    public function processAddForm() {  
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $category = $_POST['category'] ?? '';
            $image_path = ''; // Process image upload if needed
            
            // Insert product using PDO
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_path) 
                                  VALUES (?, ?, ?, ?, ?)");
            
            $result = $stmt->execute([
                $name, $description, $price, $category, $image_path
            ]);
            
            if($result) {
                header('Location: index.php?page=admin&status=product_added');
                exit;
            } else {
                header('Location: index.php?page=add_product&error=1');
                exit;
            }
        }
    }
    
}