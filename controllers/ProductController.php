<?php
require_once 'config/config.php';

class ProductController {
    
    /**
     * Prikazuje formu za dodavanje proizvoda
     */
    public function showAddForm() {
        require 'views/admin/add_product.php';
    }

    /**
     * Obrađuje dodavanje novog proizvoda
     */
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
    
    /**
     * Lists all products for users
     */
    public function listProducts() {
        global $pdo;
        
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        // Fetch products from database
        try {
            $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Include the view template with the data
            require 'views/user/all_products.php';
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška pri učitavanju proizvoda: " . $e->getMessage()
            ];
            redirect('home');
        }
    }
    
    /**
     * Shows details of a specific product
     * 
     * @param int $id Product ID
     */
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
            // Fetch product details
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
    
    /**
     * Prikazuje listu proizvoda za administraciju
     */
    public function listProductsAdmin() {
        global $pdo;
        
        // Provera ovlašćenja
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
    
    /**
     * Prikazuje formu za izmenu proizvoda
     * 
     * @param int $id ID proizvoda
     */
    public function showEditForm($id) {
        global $pdo;
        
        // Provera ovlašćenja
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
            // Dohvatanje proizvoda
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
            
            // Prikazivanje forme za izmenu
            require 'views/admin/edit_product.php';
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška pri učitavanju proizvoda: " . $e->getMessage()
            ];
            redirect('list_products');
        }
    }
    
    /**
     * Ažurira podatke o proizvodu
     */
    public function updateProduct() {
        global $pdo;
        
        // Provera ovlašćenja
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
            // Dohvatanje trenutne slike proizvoda
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $image_path = $currentProduct['image_path'] ?? '';
            
            // Obrada slike ako je uploadovana nova
            if (!empty($_FILES['image']['name'])) {
                // Ovde bi trebalo dodati kod za upload i obradu slike
                // Za sada ostavljamo prazno
                $image_path = ''; // Nova putanja slike
            } else if ($delete_image) {
                // Ako je označeno da se briše slika
                $image_path = '';
            }
            
            // Ažuriranje proizvoda
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
    
    /**
     * Briše proizvod
     * 
     * @param int $id ID proizvoda
     */
    public function deleteProduct($id) {
        global $pdo;
        
        // Provera ovlašćenja
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