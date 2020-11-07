<?php
include("../config.php");

if (isset($_POST["friendID"]) && isset($_POST["cid"])) {
    $friendID = $_POST["friendID"];
    $cid = $_POST["cid"];

    $sql = "update friends
    set request_status='rejected', response_date=current_timestamp
    where sent_by_cust=?
    and received_by_cust=?
    and request_status='pending'";

    $statement = $link->prepare($sql);
    $statement->bind_param('ii', $friendID, $cid);
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
