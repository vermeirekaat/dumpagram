<?php 
require_once (__DIR__ . '/DAO.php'); 

class ImagesDAO extends DAO {

    // filteren op meerdere criteria = zoekfunctie
    // default parameters meegeven
    public function selectImagesWithFilters($title = '', $description = '') {
        $sql = "SELECT * FROM `images` WHERE `title` LIKE :title "; 
        // array maken met key en values om parameters te binden
        $bindValues = array(); 
        $bindValues[':title'] = '%'.$title.'%';

        // indien een description opgegeven nemen we dit mee als filter, anders niet
        if (!empty($description)){
            $sql .= "AND `description` LIKE :description ORDER BY `id` ASC LIMIT 9"; 
            $bindValues[':description'] = '%'.$description.'%'; 
        }
 
        $stmt = $this->pdo->prepare($sql); 
        // array bindValues meegeven met execute 
        $stmt->execute($bindValues); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function selectRecentImages() {
        // recent toegevoegde images toevoegen, aangezien het id telkens optelt, zal dit oplopend opgehaald worden 
        $sql = "SELECT * FROM `images` ORDER BY `id` DESC LIMIT 12"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function orderPopularImages() {
        // populairste images zoeken
        // gebruik maken van INNER JOIN om de tabel images te koppelen aan images_reactions
        $sql = "SELECT `images`.*, COUNT(`images_reactions`.`image_id`) AS 'popular' FROM `images_reactions` INNER JOIN `images` ON `images_reactions`.`image_id`=`images`.`id` GROUP BY `images_reactions`.`image_id` ORDER BY COUNT(`images_reactions`.`image_id`) DESC LIMIT 12"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    
    public function selectPopularImages($id) {
        // gegevens van populairste images koppelen 
        $sql = "SELECT * FROM `images` WHERE `id` = :id LIMIT 9";
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue('id', $id); 
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectById($id) {
        // deze functie wordt gebruikt binnen de uploadImage()
        $sql = "SELECT * FROM `images` WHERE `id` = :id"; 
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue('id', $id); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function uploadImage($data) {
        // nog eens checken of er geen fouten zitten i/h form 
        $errors = $this->validate($data); 
        // gegevens invoegen i/d database als er geen fouten zijn 
        if (empty($errors)) {
            $sql = "INSERT INTO `images` (`path`, `title`, `description`) VALUES (:path, :title, :description)"; 
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':path', $data['image']);
            $stmt->bindValue(':title', $data['title']); 
            $stmt->bindValue(':description', $data['description']); 
            if($stmt->execute()) {
                return $this->selectById($this->pdo->lastInsertId());
            }
        }
        return false; 
    }

    public function validate($data) {
        $errors = []; 
        if(empty($data['title'])) {
            $errors['title'] = 'Please fill in a title'; 
        }
        if(empty($data['description'])) {
            $errors['description'] = 'Please fill in the description'; 
        }
        if(!isset($data['image'])) {
            $errors['path'] = 'Please insert a path'; 
        }
        return $errors; 
    }
}