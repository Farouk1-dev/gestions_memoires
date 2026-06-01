<?php

include("config/database.php");

$nom = "Administrateur";

$email = "admin@gmail.com";

$telephone = "01919191";

$password = password_hash(
    "admin123",
    PASSWORD_DEFAULT
);

$role = "admin";

$sql = "
INSERT INTO utilisateurs(
    nom,
    email,
    telephone,
    password,
    role
)
VALUES(
    :nom,
    :email,
    :telephone,
    :password,
    :role
)
";

$requete = $conn->prepare($sql);

$requete->execute([

    ":nom" => $nom,
    ":email" => $email,
    ":telephone" => $telephone,
    ":password" => $password,
    ":role" => $role

]);

echo "Administrateur créé avec succès";

?>