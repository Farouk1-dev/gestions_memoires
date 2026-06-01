<?php

class Interaction{

    private $conn;

    public function __construct($conn){

        $this->conn = $conn;
    }

    /* ================= MEMOIRES ================= */

    public function getAllMemoires(){

        $sql = "

            SELECT

                memoires.*,

                utilisateurs.nom,

                (
                    SELECT COUNT(*)
                    FROM likes
                    WHERE likes.memoire_id = memoires.id
                ) AS total_likes,

                (
                    SELECT COUNT(*)
                    FROM commentaires
                    WHERE commentaires.memoire_id = memoires.id
                ) AS total_commentaires

            FROM memoires

            INNER JOIN utilisateurs
            ON memoires.utilisateur_id = utilisateurs.id

            ORDER BY memoires.id DESC

        ";

        $req = $this->conn->query($sql);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}