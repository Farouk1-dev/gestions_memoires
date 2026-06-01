<?php

class Soumission{

    private $conn;

    public function __construct($conn){

        $this->conn = $conn;
    }

    /* ================= AJOUTER ================= */

    public function ajouter(
        $memoire_id,
        $etudiant_id,
        $professeur_id,
        $commentaire
    ){

        $sql = "
            INSERT INTO soumissions(
                memoire_id,
                etudiant_id,
                professeur_id,
                commentaire
            )
            VALUES(?,?,?,?)
        ";

        $req = $this->conn->prepare($sql);

        return $req->execute([
            $memoire_id,
            $etudiant_id,
            $professeur_id,
            $commentaire
        ]);
    }

    /* ================= LISTE ================= */
        public function getSoumissionsEtudiant(
            $etudiant_id
        ){

            $sql = "

                SELECT

                    soumissions.*,

                    memoires.titre,

                    utilisateurs.nom

                FROM soumissions

                INNER JOIN memoires
                ON soumissions.memoire_id = memoires.id

                INNER JOIN utilisateurs
                ON soumissions.professeur_id = utilisateurs.id

                WHERE soumissions.etudiant_id = ?

                ORDER BY soumissions.id DESC

            ";

            $req = $this->conn->prepare($sql);

            $req->execute([
                $etudiant_id
            ]);

            return $req->fetchAll(
                PDO::FETCH_ASSOC
            );
        }
}