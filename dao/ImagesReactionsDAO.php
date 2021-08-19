<?php 

require_once (__DIR__ . '/DAO.php'); 

class ImagesReactionsDAO extends DAO {
    
    public function countReactionByImage($image_id) {
        // het totaal aantal reacties van een image optellen
        $sql = "SELECT COUNT(*) AS `amount` FROM `images_reactions` WHERE `image_id` = :image_id"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue(':image_id', $image_id); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function countAmazedByImage($image_id, $reaction_id) {
        // het aantal specifieke reacties optellen per image
        $sql = "SELECT COUNT(*) AS `reaction_amount` FROM `images_reactions` WHERE `image_id` = :image_id AND `reaction_id` = :reaction_id";
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue(':image_id', $image_id); 
        $stmt->bindValue(':reaction_id', $reaction_id); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function selectById($reaction_id) {
        // deze functie wordt gebruikt in de functie insertReaction 
        $sql = "SELECT * FROM `images_reactions` WHERE `reaction_id` = :reaction_id"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue('reaction_id', $reaction_id);
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function insertReaction($data) {
        // nog eens checken of er geen fouten zitten i/h form 
        $errors = $this->validate($data); 
        // gegevens invoegen i/d database als er geen fouten zijn 
        if(empty($errors)) {
            $sql = "INSERT INTO `images_reactions` (`image_id`, `reaction_id`) VALUES (:image_id, :reaction_id)"; 
            $stmt = $this->pdo->prepare($sql); 
            $stmt->bindValue(':image_id', $data['image_id']); 
            $stmt->bindValue(':reaction_id', $data['reaction_id']); 
            // dit zal ervoor zorgen dat deze reaction meteen opgenomen wordt in het amount van de reacties bij de redirect
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
        if (!isset($data['reaction_id'])) {
            $errors['reaction_id'] = 'Please fill in reaction_id'; 
        }
    }
}