<?php 

class ProductModel {

    
    public function addProduct($data) {
            global $pdo;
        try {
            $stmt = $this->$pdo->prepare("INSERT INTO products (name, price, category_id) VALUES (:name, :price, :category_id)");
            
            return $stmt->execute([
                ':name' => $data['name'],
                ':price' => $data['price'],
                ':category_id' => $data['category_id']
            ]);
        } catch(PDOException $e) {
            // Logovanje greške
            error_log("Greška pri dodavanju proizvoda: " . $e->getMessage());
            return false;
        }
    }
}