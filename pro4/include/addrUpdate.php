<?php

include("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    $cid = $_SESSION['id'];
    $new_apt = $_POST['apt'];
    $new_block_id = $_POST['block'];
    $new_city_id = $_POST['city'];



    $sql2 = "CALL updateAddress(?, ?, ?, ?)";
        $stmt2 = $link->prepare($sql2);
        $stmt2->bind_param('iiii', $cid, $new_city_id, $new_block_id, $new_apt);
        //$stmt2->execute();
        //run the store proc
        if (!$stmt2->execute()) {
            die( "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }

        session_destroy();
        header("location: ../login.php");
        

    $stmt2->close();
    $link->close();
}
?>