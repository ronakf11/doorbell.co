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
    height: 80px;
    width: 80px;
    border-radius: 50%;
  }
</style>


<script type="text/javascript"> 
function approved(buttonID,tempCustID,cid) { 

  $.ajax({
                type: "POST",
                url: "include/approvalRequest.php",
                data: {
                   // city: 'tempCustID='+tempCustID+'&cid='+cid
                   tempCustID:tempCustID,
                   cid:cid
                }
            }).done(function(data) {
              $('#'+buttonID).html('Approved');
              $('#'+buttonID).attr("disabled", true);
              //alert(data);
              //$("#response").html(data);
            });
  
   // alert(a);
} 
</script>

<body>
  <?php include "include/nav.php" ; 
  ?>
    <h1 style="padding-top: 20px;" class=" container">Pending Approvals</h1>
    <!-- START OF FREINDS -->
    <?php
          $cid = $_SESSION['id'];
          //echo $_SESSION['id'];
          $sql = "select t.temp_cust_id,t.cust_name,t.cust_intro from request_approval r,register_temp t
          where r.temp_cust_id=t.temp_cust_id
          and r.approval_status='pending'
          and r.approver_id=?";
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
              echo "<img src='https://bootdey.com/img/Content/avatar/avatar7.png' alt='user' class='profile-photo-lg'>";
              echo "</div><div class='col-md-7 col-sm-7'>";
              echo "<h5><a href='#' class='profile-link'>" . $row["cust_name"] . "</a></h5><p>".$row["cust_intro"]."</p></div>";
              echo "<div class='col-md-3 col-sm-3'>";
              $buttonID="appr".$row["temp_cust_id"];
              $tempCustID=$row["temp_cust_id"];
              echo "<button id=".$buttonID." class='btn btn-primary pull-right' onclick='approved(\"$buttonID\",\"$tempCustID\",\"$cid\")'>" . "Approve" . "</button></div>";
              echo "</div></div>";
            }
            $result->close();
            echo "</div></div></div></div></div>";
          } else {
            echo "<div class='tab-pane fade show active' id='v-pills-home' role='tabpanel' aria-labelledby='v-pills-home-tab'><div class='container'>No approval requests pending</div></div>";
          }
          $statement->close();
          $link->close();
          ?>

  <?php include "include/footer.php" ?>