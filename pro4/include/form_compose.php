<?php
include("../config.php");
session_start();
$cid = $_SESSION['id'];
if (isset($_POST["rtype"])) {
    // Capture selected rtype
    $cid = $_SESSION['id'];
    $rtype = $_POST["rtype"];

switch ($rtype) {
    case 'friend':
        $sql = "select cust_id as friend_id, cust_name as Friend_Name from customer where cust_id in(
        select received_by_cust as friends from FRIENDS
        where sent_by_cust = ?
        and request_status='approved'
        union
        select sent_by_cust as friends from FRIENDS
        where received_by_cust = ?
        and request_status='approved')";
        $statement = $link->prepare($sql);
        $statement->bind_param('ii', $cid, $cid);
        $statement->execute();
        $result = $statement->get_result();
    if ($result->num_rows > 0) {

        echo "<div class='form-group'>   
                <label>Friends</label>
                <select name='rname' id='rid' class='form-control' required>
                <option value='Select'>Select</option>
                ";
        // output data of each row
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            echo "<option value=" . $row["friend_id"] . ">" . $row["Friend_Name"] . "</option>";
        }
        $result->close();
        echo "</select> </div>";
    } else {
        echo "No Friends";
    }
        break;
        case 'direct_neighbour':
            $sql = "select n.neighbour_id as nid,c.cust_name as neighbour_name from direct_neighbours n, customer c
          where n.neighbour_id=c.cust_id
          and c.active_status='active' 
          and n.user_id=?";
          $statement = $link->prepare($sql);
          $statement->bind_param('i', $cid);
          $statement->execute();
          $result = $statement->get_result();
        if ($result->num_rows > 0) {
    
            echo "<div class='form-group'>   
                    <label>Neighbours</label>
                    <select name='rname' id='rid' class='form-control' required>
                    <option value='Select'>Select</option>
                    ";
            // output data of each row
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                echo "<option value=" . $row["nid"] . ">" . $row["neighbour_name"] . "</option>";
            }
            $result->close();
            echo "</select> </div>";
        } else {
            echo "No Neighbours";
        }
            break;
}
}
?>