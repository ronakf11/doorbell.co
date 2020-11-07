<?php
include("../config.php");

if (isset($_POST["msgID"]) && isset($_POST["cid"])) {
    $msgID = $_POST["msgID"];
    $cid = $_POST["cid"];

    $sql = "update message_broadcast
            set message_read='READ'
            where message_id=?
            and message_receiver_id=? AND message_read='UNREAD'";

    $statement = $link->prepare($sql);
    $statement->bind_param('ii', $msgID, $cid);
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