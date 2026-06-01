<?php

require_once(__DIR__ . "/../config/database.php");

class Commentaire {

    private $conn;

    public function __construct(){

        global $conn;

        $this->conn = $conn;
    }

    /*
    ==========================
    AJOUTER COMMENTAIRE
    ==========================
    */

    public function ajouter($memoire_id, $utilisateur_id, $commentaire){

        $query = $this->conn->prepare("
            INSERT INTO commentaires
            (memoire_id, utilisateur_id, commentaire)
            VALUES (?, ?, ?)
        ");

        return $query->execute([
            $memoire_id,
            $utilisateur_id,
            $commentaire
        ]);
    }

    /*
    ==========================
    TOUS LES COMMENTAIRES
    ==========================
    */

    public function getByMemoire($memoire_id){

        $query = $this->conn->prepare("
            SELECT commentaires.*,
                   utilisateurs.nom
            FROM commentaires
            INNER JOIN utilisateurs
            ON commentaires.utilisateur_id = utilisateurs.id
            WHERE memoire_id = ?
            ORDER BY commentaires.id DESC
        ");

        $query->execute([$memoire_id]);

        return $query->fetchAll();
    }

    /*
    ==========================
    NOMBRE COMMENTAIRES
    ==========================
    */

    public function countByMemoire($memoire_id){

        $query = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM commentaires
            WHERE memoire_id = ?
        ");

        $query->execute([$memoire_id]);

        $resultat = $query->fetch();

        return $resultat['total'];
    }
}
?>