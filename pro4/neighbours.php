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
function addNeighbour(buttonID,neighbourID,cid) { 

  $.ajax({
                type: "POST",
                url: "include/addNeighbour.php",
                data: {
                   // city: 'tempCustID='+tempCustID+'&cid='+cid
                   neighbourID:neighbourID,
                   cid:cid
                }
            }).done(function(data) {
              $('#'+buttonID).html('Added');
              //$('#'+buttonID).attr("disabled", true);
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
          <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Neighbours</a>
          <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Add Neighbours</a>
          <!-- <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Requests Pending</a> -->
          <!-- <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Add Friends</a> -->
        </div>
      </div>
      <div class="col-9">
        <div class="tab-content" id="v-pills-tabContent">
          <?php
          $cid = $_SESSION['id'];
          //echo $_SESSION['id'];
          $sql = "select n.neighbour_id,c.cust_name as neighbour_name from direct_neighbours n, customer c
          where n.neighbour_id=c.cust_id
          and c.active_status='active' 
          and n.user_id=?";
          //echo "$sql";
          $statement = $link->prepare($sql);
          $statement->bind_param('i', $cid);
          $statement->execute();
          $result = $statement->get_result();
          //  $result = $link->query($sql);
          if ($result->num_rows > 0) {
            echo "<div class='tab-pane fade show active' id='v-pills-home' role='tabpanel' aria-labelledby='v-pills-home-tab'><div class='container'> <div class='row'> <div class='col-md-8'><div class='people-nearby'>";
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
              echo "<div class='nearby-user'><div class='row'><div class='col-md-2 col-sm-2'>";
              //echo "<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="user" class="profile-photo-lg">";
              echo "</div><div class='col-md-7 col-sm-7'>";
              echo "<h5><a href='#' class='profile-link'>" . $row["neighbour_name"] . "</a></h5></div>";
              // echo "<div class='col-md-3 col-sm-3'>";
              // echo "<button class='btn btn-primary pull-right'>" . "Remove" . "</button></div>";
              echo "</div></div>";
            }
            $result->close();
            echo "</div></div></div></div></div>";
          } else {
            echo "<div class='tab-pane fade show active' id='v-pills-home' role='tabpanel' aria-labelledby='v-pills-home-tab'><div class='container'>No Neighburs</div></div>";
          }
          $statement->close();
          //$link->close();
          ?>

<?php
          //$cid = $_SESSION['id'];
          //echo $_SESSION['id'];
          $sql1 = "select cust_id as neighbours_id,cust_name as neighbour_name from customer 
          where block_id=(	
            select block_id from customer 
            where cust_id=?
            and active_status='active')
          and active_status='active'
          and cust_id <> ?
          and cust_id not in (select neighbour_id from direct_neighbours
        where user_id=?)";
          //echo "$sql";
          $stmt1 = $link->prepare($sql1);
          $stmt1->bind_param('iii', $cid, $cid, $cid);
          $stmt1->execute();
          $result = $stmt1->get_result();
          //  $result = $link->query($sql);
          if ($result->num_rows > 0) {
            echo "<div class='tab-pane fade' id='v-pills-profile' role='tabpanel' aria-labelledby='v-pills-profile-tab'><div class='container'> <div class='row'> <div class='col-md-8'><div class='people-nearby'>";
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
              echo "<div class='nearby-user'><div class='row'><div class='col-md-2 col-sm-2'>";
              //echo "<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="user" class="profile-photo-lg">";
              echo "</div><div class='col-md-7 col-sm-7'>";
              echo "<h5><a href='#' class='profile-link'>" . $row["neighbour_name"] . "</a></h5></div>";
              echo "<div class='col-md-3 col-sm-3'>";
              $buttonID="nb".$row["neighbours_id"];
              $neighbourID=$row["neighbours_id"];
              echo "<button id=". $buttonID." class='btn btn-primary pull-right' onclick='addNeighbour(\"$buttonID\",\"$neighbourID\",\"$cid\")'>" ."Add as Neighbour". "</button></div>";
              echo "</div></div>";
            }
            $result->close();
            echo "</div></div></div></div></div>";
          } else {
            echo "<div class='tab-pane fade' id='v-pills-profile' role='tabpanel' aria-labelledby='v-pills-profile-tab'><div class='container'>"."No New Members In Block To Add"."</div></div>";
          }
          $stmt1->close();
          $link->close();
          ?>
          
        </div>
      </div>
    </div>
  </div>


  <?php include "include/footer.php" ?>