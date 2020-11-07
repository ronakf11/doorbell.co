<?php
// Initialize the session
session_start();
//echo "nav".$_SESSION['id'];

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
  <div class="container">
    <a class="navbar-brand" href="welcome.php">
      <img src="img/logo.png" width="100" height="60" alt="">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'welcome.php') {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="welcome.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'approval.php') {
                              echo 'active';
                            } ?>">
          <a class="nav-link" href="approval.php">Pending Approvals</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if (basename($_SERVER['PHP_SELF']) == 'friends.php' or basename($_SERVER['PHP_SELF']) == 'neighbours.php') {
                                                echo 'active';
                                              } ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Connections
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item <?php if (basename($_SERVER['PHP_SELF']) == 'friends.php') {
                                      echo 'active';
                                    } ?>" href="friends.php">Friends</a>
            <a class="dropdown-item <?php if (basename($_SERVER['PHP_SELF']) == 'neighbours.php') {
                                      echo 'active';
                                    } ?>" href="neighbours.php">Neighbours</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-toggle="modal" data-target="#exampleModal">Initiate Thread</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if (basename($_SERVER['PHP_SELF']) == 'address.php' or basename($_SERVER['PHP_SELF']) == 'profile.php' or basename($_SERVER['PHP_SELF']) == 'contact.php') {
                                                echo 'active';
                                              } ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Account Details
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item <?php if (basename($_SERVER['PHP_SELF']) == 'address.php') {
                                      echo 'active';
                                    } ?>" href="address.php">Change Address</a>
            <a class="dropdown-item <?php if (basename($_SERVER['PHP_SELF']) == 'profile.php') {
                                      echo 'active';
                                    } ?>" href="profile.php">Profile</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item <?php if (basename($_SERVER['PHP_SELF']) == 'contact.php') {
                                      echo 'active';
                                    } ?>" href="contact.php">Contact Us</a>
            <a class="dropdown-item" href="logout.php">Sign Out</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Initiate Thread</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form name="form1" action="../pro4/include/createThread.php" method="POST">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Receipent Type</label>
            <select name="rtype" class="form-control" id="exampleFormControlSelect1" required>
              <option value="hood">Hood</option>
              <option value="block">Block</option>
              <option value="friend">Friend</option>
              <option value="direct_neighbour">Neighbour</option>
              <option value="allfriends">All Friends</option>
            </select>
          </div>
          <div id="response" class='form-group'>

          </div>
          <div class="form-group">
            <label for="subjectID" class="col-form-label">Subject:</label>
            <input type="text" name="subject" class="form-control" id="subjectID" required />
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Content:</label>
            <textarea name="content" class="form-control" id="message-text form1" required></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Send message</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {
    $("#exampleFormControlSelect1").change(function() {
      var rtype = $("#exampleFormControlSelect1 option:selected").val();
      $.ajax({
        type: "POST",
        url: "include/form_compose.php",
        data: {
          rtype: rtype
        }
      }).done(function(data) {
        $("#response").html(data);
      });
    });
  });
</script>