<?php


include("../config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $thread_subject = $_POST["subject"];
  $thread_content = $_POST["content"];
  $receipent_type = $_POST["rtype"];
  $initiated_by = $_SESSION["id"];
  $gps_coords = NULL;
  //echo $thread_subject . $receipent_type . $initiated_by;
  if (isset($_POST["rname"])) {
    $receipent_id = $_POST["rname"];
  } else {
    $receipent_id = NULL;
  }
  $sql = "CALL createThread(?,?,?,?,?,?)";
  $statement = $link->prepare($sql);
  $statement->bind_param('ssisid', $thread_subject, $thread_content, $initiated_by, $receipent_type, $receipent_id, $gps_coords);
  if (!$statement->execute()) {
    die("CALL failed");
    //die("ddd");
  } else {
    //echo "success";
  }
  $statement->close();
}
header("location: ../welcome.php");
?>