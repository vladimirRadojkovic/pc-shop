<?php
require_once 'config/config.php';

class ProductController {
    
    public function showAddForm() {
        require 'views/admin/add_product.php';
    }

    public function processAddForm() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $category = $_POST['category'] ?? '';
            $image_path = '';
            
            if (empty($name) || empty($price)) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Naziv i cena su obavezni!"
                ];
                header('Location: index.php?page=add_product');
                exit;
            }
            
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image_path) 
                                      VALUES (?, ?, ?, ?, ?)");
                
                $result = $stmt->execute([
                    $name, $description, $price, $category, $image_path
                ]);
                
                if($result) {
                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'message' => "Proizvod uspešno dodat!"
                    ];
                    header('Location: index.php?page=admin&status=product_added');
                    exit;
                } else {
                    $_SESSION['alert'] = [
                        'type' => 'danger',
                        'message' => "Database error: " . print_r($stmt->errorInfo(), true)
                    ];
                    header('Location: index.php?page=add_product&error=1');
                    exit;
                }
            } catch (PDOException $e) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => $e->getMessage()
                ];
                header('Location: index.php?page=add_product');
                exit;
            }
        } else {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Proizvod uspešno dodat!'
            ];
            header('Location: index.php?page=add_product');
            exit;
        }
    }
}