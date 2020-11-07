<?php
include("../config.php");

if (isset($_POST["cid"])) {
    $cid = $_POST["cid"];

    $sql = "select concat(cust_hood_details,'^^',cust_block_details) as cust_all_details
    from 
     (select group_concat(cust_per_block_details SEPARATOR '%%') as cust_hood_details ,
     (select CONCAT (d.block_latitude,'&&',d.block_longitude) 
      from customer c, blocks d
     where c.block_id=d.block_id
     and c.cust_id=?) as cust_block_details
     from (  
     select CONCAT (count(a.cust_name),'$$', b.block_name ,'$$',b.block_latitude,'$$',b.block_longitude) 
     as cust_per_block_details
     from customer a, blocks b
              where a.block_id=b.block_id
              and b.hood_id = (select hood_id from customer, blocks 
                where cust_id=?
                and customer.block_id=blocks.block_id)
              and a.active_status='active'
              and a.cust_id <> ?
              group by b.block_id,b.block_latitude,b.block_longitude) f)g";

    $statement = $link->prepare($sql);
    $statement->bind_param('iii', $cid, $cid, $cid);
    if (!$statement->execute()) {
        die( "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
    }
    else{
        $res = $statement->get_result();
        if ($res->num_rows > 0) {
            // output data of each row
            $row = $res->fetch_array(MYSQLI_ASSOC);
            echo $row['cust_all_details'];
            }
      //  echo "success";
    }
    $statement->close();
    }
   

$link->close();
?>
