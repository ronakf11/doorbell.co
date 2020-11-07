<?php include "include/header.php" ?>
<style type="text/css">
  body {
    background: #FAFAFA;
  }

  /*==================================================
  Nearby People CSS
  ==================================================*/

  .people-nearby .google-maps {
    background: #f8f8f8;
    border-radius: 4px;
    border: 1px solid #f1f2f2;
    padding: 20px;
    margin-bottom: 20px;
  }

  .people-nearby .google-maps .map {
    height: 300px;
    width: 100%;
    border: none;
  }

  .people-nearby .nearby-user {
    padding: 20px 0;
    border-top: 1px solid #f1f2f2;
    border-bottom: 1px solid #f1f2f2;
    margin-bottom: 20px;
  }

  img.profile-photo-lg {
    height: 40px;
    width: 40px;
    border-radius: 50%;
  }
</style>

<style type="text/css">
  .aa {
    border-right: 2px solid #333;
  }
</style>


<script type="text/javascript"> 
function addFriend(buttonID,friendID,cid) { 
debugger;
  $.ajax({
                type: "POST",
                url: "include/addFriend.php",
                data: {
                   // city: 'tempCustID='+tempCustID+'&cid='+cid
                   friendID:friendID,
                   cid:cid
                }
            }).done(function(data) {
              $('#'+buttonID).html('Sent');
              $('#'+buttonID).attr("disabled", true);
              console.log(data);
             // window.location.reload()
              //$("#response").html(data);
            });
  
   // alert(a);
} 

function acceptFriend(buttonID,friendID,cid,rejectButtonID) { 
debugger;
  $.ajax({
                type: "POST",
                url: "include/acceptFriend.php",
                data: {
                   // city: 'tempCustID='+tempCustID+'&cid='+cid
                   friendID:friendID,
                   cid:cid
                }
            }).done(function(data) {
              $('#'+buttonID).html('Accepted');
              $('#'+buttonID).attr("disabled", true);
              $('#'+rejectButtonID).hide();
              console.log(data);
              //alert(data);
              //$("#response").html(data);
            });
  
   // alert(a);
} 

function rejectFriend(buttonID,friendID,cid,acceptButtonID) { 

  $.ajax({
                type: "POST",
                url: "include/rejectFriend.php",
                data: {
                   // city: 'tempCustID='+tempCustID+'&cid='+cid
                   friendID:friendID,
                   cid:cid
                }
            }).done(function(data) {
              $('#'+buttonID).html('Rejected');
              $('#'+buttonID).attr("disabled", true);
              $('#'+acceptButtonID).hide();
              console.log(data);
              //alert(data);
              //$("#response").html(data);
            });
  
   // alert(a);
} 
</script>
<body>
  <?php include "include/nav.php" ?>



  <div style="padding-top: 50px;" class="container">
    <div class="row">
      <div class="col-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Friends</a>
          <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Received Friend Requests</a>
          <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Sent Friend Requests</a>
          <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Add Friends</a>
        </div>
      </div>
      <div class="col-9">
        <div class="tab-content" id="v-pills-tabContent">
          <?php
          $cid = $_SESSION['id'];
          //echo $_SESSION['id'];
          $sql = "select cust_name as Friend_Name from customer where cust_id in(
              select received_by_cust as friends from FRIENDS
              where sent_by_cust = ?
              and request_status='approved'
              union
              select sent_by_cust as friends from FRIENDS
              where received_by_cust = ?
              and request_status='approved')";
          //echo "$sql";
          $statement = $link->prepare($sql);
          $statement->bind_param('ii', $cid, $cid);
          $statement->execute();
          $result = $statement->get_result();
          //  $result = $link->query($sql);
          if ($result->num_rows > 0) {
            echo "<div class='tab-pane fade show active' id='v-pills-home' role='tabpanel' aria-labelledby='v-pills-home-tab'><div class='container'> <div class='row'> <div class='col-md-8'><div class='people-nearby'>";
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
              echo "<div class='nearby-user'><div class='row'><div class='col-md-2 col-sm-2'>";
              //echo "<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="user" class="profile-photo-lg">";
              echo "</div><div class='col-md-7 col-sm-7'>";
              echo "<h5><a href='#' class='profile-link'>" . $row["Friend_Name"] . "</a></h5></div>";
              // echo "<div class='col-md-3 col-sm-3'>";
              // echo "<button class='btn btn-primary pull-right'>" . "Remove" . "</button></div>";
              echo "</div></div>";
            }
            $result->close();
            echo "</div></div></div></div></div>";
          } else {
            echo "<div class='tab-pane fade show active' id='v-pills-home' role='tabpanel' aria-labelledby='v-pills-home-tab'><div class='container'>No friends</div></div>";
          }
          $statement->close();
          // $link->close();
          ?>

          <?php
          //$cid = $_SESSION['id'];
          //echo $_SESSION['id'];
          $sql1 = "select sent_by_cust as Friend_Id,c.cust_name as Friend_Name   from FRIENDS f, customer c
          where 
          f.sent_by_cust=c.cust_id
          and received_by_cust = ?
          and request_status = 'pending'";
          //echo "$sql";
          $stmt1 = $link->prepare($sql1);
          $stmt1->bind_param('i', $cid);
          $stmt1->execute();
          $result = $stmt1->get_result();
          //  $result = $link->query($sql);
          if ($result->num_rows > 0) {
            echo "<div class='tab-pane fade' id='v-pills-profile' role='tabpanel' aria-labelledby='v-pills-profile-tab'><div class='container'> <div class='row'> <div class='col-md-8'><div class='people-nearby'>";
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
              echo "<div class='nearby-user'><div class='row'><div class='col-md-2 col-sm-2'>";
              //echo "<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="user" class="profile-photo-lg">";
              echo "</div><div class='col-md-7 col-sm-7'>";
              echo "<h5><a href='#' class='profile-link'>" . $row["Friend_Name"] . "</a></h5></div>";
              echo "<div class='col-md-3 col-sm-3'>";
              $acceptbuttonID="fra".$row["Friend_Id"];
              $rejectbuttonID="frr".$row["Friend_Id"];
              $friendID=$row["Friend_Id"];
              echo "<button id=". $acceptbuttonID." class='btn btn-primary pull-right' onclick='acceptFriend(\"$acceptbuttonID\",\"$friendID\",\"$cid\",\"$rejectbuttonID\")'>" . "Accept" . "</button>";
              echo "<button id=". $rejectbuttonID." class='btn btn-primary pull-right' onclick='rejectFriend(\"$rejectbuttonID\",\"$friendID\",\"$cid\",\"$acceptbuttonID\")'>" . "Reject" . "</button></div>";
              echo "</div></div>";
            }
            $result->close();
            echo "</div></div></div></div></div>";
          } else {
            echo "<div class='tab-pane fade' id='v-pills-profile' role='tabpanel' aria-labelledby='v-pills-profile-tab'><div class='container'>No New Friend Requests</div></div>";
          }
          $stmt1->close();
          //$link->close();
          ?>





          <?php
          //$cid = $_SESSION['id'];
          //echo $_SESSION['id'];
          $sql1 = "select received_by_cust as Friend_Id,c.cust_name as Friend_Name   from FRIENDS f, customer c
          where 
          f.received_by_cust=c.cust_id
          and sent_by_cust = ?
          and request_status = 'pending'";
          //echo "$sql";
          $stmt1 = $link->prepare($sql1);
          $stmt1->bind_param('i', $cid);
          $stmt1->execute();
          $result = $stmt1->get_result();
          //  $result = $link->query($sql);
          if ($result->num_rows > 0) {
            echo "<div class='tab-pane fade' id='v-pills-messages' role='tabpanel' aria-labelledby='v-pills-messages-tab'><div class='container'> <div class='row'> <div class='col-md-8'><div class='people-nearby'>";
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
              echo "<div class='nearby-user'><div class='row'><div class='col-md-2 col-sm-2'>";
              //echo "<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="user" class="profile-photo-lg">";
              echo "</div><div class='col-md-7 col-sm-7'>";
              echo "<h5><a href='#' class='profile-link'>" . $row["Friend_Name"] . "</a></h5></div>";
              // echo "<div class='col-md-3 col-sm-3'>";
              // echo "<button class='btn btn-primary pull-right'>" . "Remove" . "</button></div>";
              echo "</div></div>";
            }
            $result->close();
            echo "</div></div></div></div></div>";
          } else {
            echo "<div class='tab-pane fade' id='v-pills-messages' role='tabpanel' aria-labelledby='v-pills-messages-tab'><div class='container'>No Sent Friend Requests</div></div>";
          }
          $stmt1->close();
          //$link->close();
          ?>
          <?php
          //$cid = $_SESSION['id'];
          //echo $_SESSION['id'];
          $sql1 = "select a.cust_id as Friend_Id, a.cust_name as Friend_Name from customer a, blocks b
          where a.block_id=b.block_id
          and b.hood_id = (select hood_id from customer, blocks 
            where cust_id=?
            and customer.block_id=blocks.block_id)
          and a.active_status='active'
          and a.cust_id <> ?
          and a.cust_id not in (
                     select received_by_cust as friends from FRIENDS 
                  where sent_by_cust = ?
                  and request_status in ('approved','pending')
                  union
                  select sent_by_cust as friends from FRIENDS 
                  where received_by_cust = ?
                  and request_status in ('approved','pending'))";
          //echo "$sql";
          $stmt1 = $link->prepare($sql1);
          $stmt1->bind_param('iiii', $cid, $cid,$cid,$cid);
          $stmt1->execute();
          $result = $stmt1->get_result();
          //  $result = $link->query($sql);
          if ($result->num_rows > 0) {
            echo "<div class='tab-pane fade' id='v-pills-settings' role='tabpanel' aria-labelledby='v-pills-settings-tab'><div class='container'> <div class='row'> <div class='col-md-8'><div class='people-nearby'>";
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
              echo "<div class='nearby-user'><div class='row'><div class='col-md-2 col-sm-2'>";
              //echo "<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="user" class="profile-photo-lg">";
              echo "</div><div class='col-md-7 col-sm-7'>";
              echo "<h5><a href='#' class='profile-link'>" . $row["Friend_Name"] . "</a></h5></div>";
              echo "<div class='col-md-3 col-sm-3'>";
              $buttonID="fr".$row["Friend_Id"];
              $friendID=$row["Friend_Id"];
              echo "<button id=". $buttonID." class='btn btn-primary pull-right' onclick='addFriend(\"$buttonID\",\"$friendID\",\"$cid\")'>" . "Add as Friend" . "</button></div>";
              echo "</div></div>";
            }
            $result->close();
            echo "</div></div></div></div></div>";
          } else {
            echo "<div class='tab-pane fade' id='v-pills-settings' role='tabpanel' aria-labelledby='v-pills-settings-tab'><div class='container'>No New Members In Hood To Add</div></div>";
          }
          $stmt1->close();
          $link->close();
          ?>
        </div>
      </div>
    </div>
  </div>

  <?php include "include/footer.php" ?>