<?php
require_once("config.php");

if (!empty($_POST["email"])) {
    $email = $_POST["email"];
    $sql = "SELECT email FROM userdata WHERE email=:email"; // fixed column name
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $rowCount = $query->rowCount();

    if ($rowCount > 0) {
        echo "<p class='error-message'>Email already exists.</p>";
    } else {
        echo "<p class='success-message'>Email available for registration.</p>";
    }
}
?>
