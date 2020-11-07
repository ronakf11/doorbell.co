<?php
include("../config.php");

if (isset($_POST["neighbourID"]) && isset($_POST["cid"])) {
    $neighbourID = $_POST["neighbourID"];
    $cid = $_POST["cid"];

    $sql = "    CALL addneighbour( ? , ?)";
    $statement = $link->prepare($sql);
    $statement->bind_param('ii', $cid, $neighbourID);
    if (!$statement->execute()) {
        die( "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
    }
    else{
        echo "success";
    }
    $statement->close();
}
$link->close();
?>
