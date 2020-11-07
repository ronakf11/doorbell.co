<?php
include("../config.php");
session_start();

$cid = $_SESSION['id'];
// echo $cid;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (isset($_POST["reply"]) && isset($_POST["threadID"])) {
    $msgBody = $_POST["reply"];
    $tid = $_POST["threadID"];
    $gpsCoo=NULL;


        $sql = "call createNewMessage(?,?,?,?)";
        $statement = $link->prepare($sql);
        $statement->bind_param('iisd', $tid,$cid,$msgBody,$gpsCoo);
                    if (!$statement->execute()) {
                        die("CALL failed");
                        //die("ddd");
                    } else {
                        echo "yayyy  ".$reply;
                    }
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
                echo $row["cust_name"];


        }

  } else {
    echo "no val in textbox";
  }
  $statement->close();
}
header("location: ../welcome.php");
?>