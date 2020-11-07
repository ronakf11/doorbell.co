<?php
include("../config.php");

if (isset($_POST["city"])) {
    $selectedCity = $_POST["city"];

    $sql = "select group_concat( concat(block_id,'$$',block_name,'$$',block_latitude,'$$',block_longitude )SEPARATOR '%%')
    as all_block_details
    from blocks where city_id=?";

    $statement = $link->prepare($sql);
        $statement->bind_param('i', $selectedCity);
    if (!$statement->execute()) {
        die( "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
    }
    else{
        $res = $statement->get_result();
        if ($res->num_rows > 0) {
            // output data of each row
            $row = $res->fetch_array(MYSQLI_ASSOC);
            echo $row['all_block_details'];
            }
      //  echo "success";
    }
    $statement->close();
    }
    

$link->close();
?>
