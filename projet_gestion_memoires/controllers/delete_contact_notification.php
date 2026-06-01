<?php

include("../config/database.php");

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $delete = $conn->prepare("
    DELETE FROM contact_admin
    WHERE id = ?
    ");

    $delete->execute([$id]);

    header(
    "Location: ../views/admin/contact_notifications.php"
    );

    exit();
}
?>