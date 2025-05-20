<?php

class ProductController {
    public function showAddForm() {
        require 'views/admin/add_product.php';
    }

    public function processAddForm() {  
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['product_name'],
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id']
            ];
            
            require_once 'models/ProductModel.php';
            $productModel = new ProductModel();
            
            if($productModel->addProduct($data)) {
                header('Location: /pc-shop/admin?status=product_added');
            } else {
                header('Location: /pc-shop/add_product?error=1');
            }
        }
    }
    
}