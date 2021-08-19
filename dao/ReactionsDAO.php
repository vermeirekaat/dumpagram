<?php 

require_once (__DIR__ . '/DAO.php'); 

class ReactionsDAO extends DAO {
    // drie verschillende reacties ophalen zodat deze getoond kunnen worden in de view 
    public function selectReactions() {
        $sql = "SELECT * FROM `reactions`";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
 