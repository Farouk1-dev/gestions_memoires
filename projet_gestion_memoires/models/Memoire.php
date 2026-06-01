<?php

class Memoire{

    private $conn;

    public function __construct($conn){

        $this->conn = $conn;
    }

    /* ================= AJOUTER ================= */

    public function ajouter(
        $utilisateur_id,
        $titre,
        $categorie,
        $resume,
        $fichier
    ){

        $sql = "
            INSERT INTO memoires(
                utilisateur_id,
                titre,
                categorie,
                resume,
                fichier
            )
            VALUES(?,?,?,?,?)
        ";

        $req = $this->conn->prepare($sql);

        return $req->execute([
            $utilisateur_id,
            $titre,
            $categorie,
            $resume,
            $fichier
        ]);
    }
    

    /* ================= MEMOIRES ETUDIANT ================= */

        public function getMemoiresByUtilisateur(
            $utilisateur_id
        ){

            $sql = "
                SELECT *
                FROM memoires
                WHERE utilisateur_id = ?
                ORDER BY id DESC
            ";

            $req = $this->conn->prepare($sql);

            $req->execute([$utilisateur_id]);

            return $req->fetchAll(PDO::FETCH_ASSOC);
        }
}