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
            $image_path = $_FILES['image']['name'] ?? '';

            $tempPath = $_FILES['image']['tmp_name'];
            $destinationPath = __DIR__ . '/../assets/img/' . $image_path;

            if (!move_uploaded_file($tempPath, $destinationPath)) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Greška prilikom premeštanja slike."
                ];
            }
            
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

    public function listProducts() {
        global $pdo;
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            require 'views/user/all_products.php';
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška pri učitavanju proizvoda: " . $e->getMessage()
            ];
            redirect('home');
        }
    }
    
    public function showProductDetails($id) {
        global $pdo;
        
        if (!$id) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Neispravan ID proizvoda."
            ];
            redirect('all_products');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Proizvod nije pronađen."
                ];
                redirect('all_products');
            }
            
            require 'views/user/product_details.php';
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška pri učitavanju detalja proizvoda: " . $e->getMessage()
            ];
            redirect('all_products');
        }
    }
    
    public function listProductsAdmin() {
        global $pdo;
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('login');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            require 'views/admin/list_products.php';
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška pri učitavanju proizvoda: " . $e->getMessage()
            ];
            redirect('admin');
        }
    }

    public function showEditForm($id) {
        global $pdo;
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('login');
        }
        
        if (!$id) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Neispravan ID proizvoda."
            ];
            redirect('list_products');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Proizvod nije pronađen."
                ];
                redirect('list_products');
            }
            
            require 'views/admin/edit_product.php';
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška pri učitavanju proizvoda: " . $e->getMessage()
            ];
            redirect('list_products');
        }
    }
    
    public function updateProduct() {
        global $pdo;
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('list_products');
        }
        
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $delete_image = isset($_POST['delete_image']);
        
        if (empty($id) || empty($name) || empty($price)) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "ID, naziv i cena su obavezni!"
            ];
            redirect('edit_product&id=' . $id);
        }
        
        try {
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $image_path = $currentProduct['image_path'] ?? '';
            
            if (!empty($_FILES['image']['name'])) {
                $image_path = ''; 
            } else if ($delete_image) {
                $image_path = '';
            }
            
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_path = ? WHERE id = ?");
            $result = $stmt->execute([$name, $description, $price, $category, $image_path, $id]);
            
            if ($result) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => "Proizvod uspešno ažuriran!"
                ];
                redirect('list_products');
            } else {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Greška pri ažuriranju proizvoda: " . print_r($stmt->errorInfo(), true)
                ];
                redirect('edit_product&id=' . $id);
            }
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška baze: " . $e->getMessage()
            ];
            redirect('edit_product&id=' . $id);
        }
    }

    public function deleteProduct($id) {
        global $pdo;
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('login');
        }
        
        if (!$id) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Neispravan ID proizvoda."
            ];
            redirect('list_products');
        }
        
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => "Proizvod uspešno obrisan!"
                ];
            } else {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Greška pri brisanju proizvoda: " . print_r($stmt->errorInfo(), true)
                ];
            }
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška baze: " . $e->getMessage()
            ];
        }
        
        redirect('list_products');
    }
}