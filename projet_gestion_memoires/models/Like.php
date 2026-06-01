<?php

require_once(__DIR__ . "/../config/database.php");

class Like {

    private $conn;

    public function __construct(){

        global $conn;

        $this->conn = $conn;
    }

    /*
    ==========================
    VERIFIER SI LIKE EXISTE
    ==========================
    */

    public function existe($memoire_id, $utilisateur_id){

        $query = $this->conn->prepare("
            SELECT *
            FROM likes
            WHERE memoire_id = ?
            AND utilisateur_id = ?
        ");

        $query->execute([
            $memoire_id,
            $utilisateur_id
        ]);

        return $query->fetch();
    }

    /*
    ==========================
    AJOUTER LIKE
    ==========================
    */

    public function ajouter($memoire_id, $utilisateur_id){

        $query = $this->conn->prepare("
            INSERT INTO likes
            (memoire_id, utilisateur_id)
            VALUES (?, ?)
        ");

        return $query->execute([
            $memoire_id,
            $utilisateur_id
        ]);
    }

    /*
    ==========================
    SUPPRIMER LIKE
    ==========================
    */

    public function supprimer($memoire_id, $utilisateur_id){

        $query = $this->conn->prepare("
            DELETE FROM likes
            WHERE memoire_id = ?
            AND utilisateur_id = ?
        ");

        return $query->execute([
            $memoire_id,
            $utilisateur_id
        ]);
    }

    /*
    ==========================
    NOMBRE DE LIKES
    ==========================
    */

    public function nombre($memoire_id){

        $query = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM likes
            WHERE memoire_id = ?
        ");

        $query->execute([$memoire_id]);

        $resultat = $query->fetch();

        return $resultat['total'];
    }
}
?>