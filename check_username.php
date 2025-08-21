<?php
include('config.php');

if(!empty($_POST["username"])) {
    $username = $_POST["username"];

    $sql = "SELECT * FROM userdata WHERE username=:uname";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uname', $username, PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {
        echo "<span style='color:red'>Username not available.</span>";
    } else {
        echo "<span style='color:green'>Username available.</span>";
    }
}
?>
