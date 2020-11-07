<?php
include("config.php");
$err = "I hope you are having a good day :)";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // username and password sent from form 

  $email = $_POST['email'];
  $mypassword = $_POST['password'];
  $sql1 = "SELECT cust_id, password as hashedPassword, active_status FROM customer WHERE cust_email = '$email'
  and active_status='active'";
  $stmt1 = $link->prepare($sql1);
  $stmt1->execute();
  $result = $stmt1->get_result();

  if ($result->num_rows > 0) {

    $row = $result->fetch_array(MYSQLI_ASSOC);
    $hashedPassword = $row['hashedPassword'];
    $cust_id = $row['cust_id'];
    $active_status = $row['active_status'];

    //echo "gaurav".$cust_id;

    if (password_verify($mypassword, $hashedPassword)) {

      session_start();

      // Store data in session variables
      $_SESSION["loggedin"] = true;
      $_SESSION["id"] = $cust_id;
      //echo $_SESSION["id"];
      $_SESSION["email"] = $email;
      // Redirect user to welcome page
      header("location: welcome.php");
    } else {
      $err = "Your Password is invalid";
    }
  } else {

    $sql2 = "SELECT cust_id FROM customer WHERE cust_email = '$email'
    and active_status='deactive'";
    $stmt2 = $link->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows > 0) {
      $err = "Your Profile is currently deactivated as you opted for an address update.We are waiting for necessary approvals.";
    } else {
      $err = "Your Email is not registered.";
    }
    $stmt2->close();
  }
  // $result->close();
  $stmt1->close();
  

  //  $link->close();
}
?>


<?php include "include/header.php" ?>

<body>
  <style type="text/css">
    body {
      font: 14px sans-serif;
    }

    .wrapper {
      width: 350px;
      padding: 20px;
    }
  </style>

  <div class="wrapper container">
    <img class="rounded mx-auto d-block" src="img/logo.png" width="200" height="120" alt="">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">

      </div>
      <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
        <span class="help-block"><?php echo $err; ?></span><button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
    </form>
  </div>
</body>

</html>