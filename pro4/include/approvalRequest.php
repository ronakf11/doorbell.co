<?php
include("../config.php");

if (isset($_POST["tempCustID"]) && isset($_POST["cid"])) {
    // Capture selected country
    $approvedCustID = $_POST["tempCustID"];
    $approverID = $_POST["cid"];

    $sql = "update request_approval
    set approval_status='approved',
    approval_date=current_timestamp 
    where temp_cust_id=?
    and approver_id=?
    and approval_status='pending'";

    $statement = $link->prepare($sql);
    $statement->bind_param('ii', $approvedCustID, $approverID);
    $statement->execute();
    $result = $statement->get_result();
    echo "success";
    // if ($result->num_rows > 0 ) {

    //     echo "success";
    // } 
}
$statement->close();
$link->close();
?>
