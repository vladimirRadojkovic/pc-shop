<?php
require_once 'config/config.php';

class CartController {
    
    /**
     * Initialize cart in session if it doesn't exist
     */
    private function initCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    /**
     * Add product to cart
     * 
     * @param int $productId
     * @param int $quantity
     */
    public function addToCart() {
        global $pdo;
        
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if ($quantity < 1) $quantity = 1;
            
            // Validate product exists
            try {
                $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$product) {
                    $_SESSION['alert'] = [
                        'type' => 'danger',
                        'message' => "Proizvod nije pronađen."
                    ];
                    redirect('all_products');
                }
                
                // Initialize cart
                $this->initCart();
                
                // Check if product already in cart
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $quantity
                    ];
                }
                
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => "Proizvod je dodat u korpu."
                ];
                redirect('cart');
                
            } catch (PDOException $e) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Greška: " . $e->getMessage()
                ];
                redirect('all_products');
            }
        } else {
            redirect('all_products');
        }
    }
    
    /**
     * Display the cart contents
     */
    public function viewCart() {
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        $this->initCart();
        require 'views/user/cart.php';
    }
    
    /**
     * Update quantity of a product in cart
     */
    public function updateCartItem() {
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if ($quantity < 1) {
                // Remove item if quantity is less than 1
                $this->removeCartItem($productId);
                return;
            }
            
            $this->initCart();
            
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
                
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => "Korpa je ažurirana."
                ];
            }
            
            redirect('cart');
        } else {
            redirect('cart');
        }
    }
    
    /**
     * Remove item from cart
     * 
     * @param int $productId
     */
    public function removeCartItem($productId = null) {
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        if ($productId === null) {
            $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }
        
        $this->initCart();
        
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => "Proizvod je uklonjen iz korpe."
            ];
        }
        
        redirect('cart');
    }
    
    /**
     * Clear the entire cart
     */
    public function clearCart() {
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        $_SESSION['cart'] = [];
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => "Korpa je ispražnjena."
        ];
        
        redirect('cart');
    }
    
    /**
     * Process checkout and create order
     */
    public function checkout() {
        global $pdo;
        
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        $this->initCart();
        
        // Check if cart is empty
        if (empty($_SESSION['cart'])) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Korpa je prazna."
            ];
            redirect('cart');
        }
        
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, created_at) VALUES (?, NOW())");
            $stmt->execute([$_SESSION['user']['id']]);
            $orderId = $pdo->lastInsertId();
            
            // Add order items
            $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            
            foreach ($_SESSION['cart'] as $item) {
                $insertItem->execute([$orderId, $item['id'], $item['quantity']]);
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Clear cart after successful checkout
            $_SESSION['cart'] = [];
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => "Narudžbina je uspešno kreirana."
            ];
            
            redirect('order_history');
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška prilikom kreiranja narudžbine: " . $e->getMessage()
            ];
            
            redirect('cart');
        }
    }
    
    /**
     * Display order history for the user
     */
    public function orderHistory() {
        global $pdo;
        
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        try {
            // Fetch user's orders
            $stmt = $pdo->prepare("
                SELECT o.id, o.created_at, 
                       SUM(oi.quantity * p.price) as total_amount,
                       COUNT(oi.id) as total_items
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$_SESSION['user']['id']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            require 'views/user/order_history.php';
            
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška: " . $e->getMessage()
            ];
            
            redirect('all_products');
        }
    }
    
    /**
     * Display order details
     * 
     * @param int $orderId
     */
    public function orderDetails($orderId = null) {
        global $pdo;
        
        // Check user authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            redirect('login');
        }
        
        if ($orderId === null) {
            $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }
        
        try {
            // Check if order belongs to user
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
            $stmt->execute([$orderId, $_SESSION['user']['id']]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => "Narudžbina nije pronađena."
                ];
                redirect('order_history');
            }
            
            // Fetch order items
            $stmt = $pdo->prepare("
                SELECT oi.quantity, p.name, p.price, (oi.quantity * p.price) as subtotal
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['subtotal'];
            }
            
            require 'views/user/order_details.php';
            
        } catch (PDOException $e) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => "Greška: " . $e->getMessage()
            ];
            
            redirect('order_history');
        }
    }
}
