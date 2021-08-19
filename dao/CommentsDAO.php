<?php 

require_once (__DIR__ . '/DAO.php'); 

class CommentsDAO extends DAO {

    public function selectImage($id) {
        // afbeelding selecteren o.b.v. het id 
        $sql = "SELECT * FROM `images` WHERE `id` = :id"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue('id', $id); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function selectByImageId($imageId) {
        // comments ophalen o.b.v. het image_id
        $sql = "SELECT * FROM `comments` WHERE `image_id`= :image_id"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue('image_id', $imageId); 
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    private function selectbyId($id) {
        // deze functie wordt gebruikt in de functie insertComment
        $sql = "SELECT * FROM `comments` WHERE `id` = :id"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue('id', $id); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function insertComment($data) {
        // nog eens checken of er geen fouten zitten i/h form 
        $errors = $this->validate($data); 
        // gegevens invoegen i/d database als er geen fouten zijn 
        if (empty($errors)) {
            $sql = "INSERT INTO `comments` (`image_id`, `comment`) VALUES (:image_id, :comment)"; 
            $stmt = $this->pdo->prepare($sql); 
            $stmt->bindValue(':image_id', $data['image_id']); 
            $stmt->bindValue(':comment', $data['comment']); 
            // dit zorgt ervoor dat de nieuwe comment op het scherm komt bij het opnieuw laden van de pagina 
            if ($stmt->execute()) {
                return $this->selectById($this->pdo->lastInsertId()); 
            }
        }
        return false; 
    }

    public function validate($data) {
        $errors = []; 
        if (!isset($data['image_id'])) {
            $errors['image_id'] = 'Please fill in image_id'; 
        }
        if (empty($data['comment'])) {
            $errors['comment'] = 'Please fill in comment'; 
        }
        return $errors; 
    }

}