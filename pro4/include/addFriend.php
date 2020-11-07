<?php
include("../config.php");

if (isset($_POST["friendID"]) && isset($_POST["cid"])) {
    $friendID = $_POST["friendID"];
    $cid = $_POST["cid"];

    $sql = "    CALL addFriend( ? , ?)";
    $statement = $link->prepare($sql);
    $statement->bind_param('ii', $cid, $friendID);
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
