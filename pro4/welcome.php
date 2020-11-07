<?php include "include/header.php" ?>


<script type="text/javascript">
  $(document).ready(function() {

    for (var i = 0; i < $('.customBtn').length; i++) {
      var id = $('.customBtn')[i].id;
      if ($('#' + id).text() == 'Read') {
        $('#' + id).attr("disabled", true);
      }
    }

  });


  function markAsRead(buttonID, msgID, cid) {
    debugger;
    $.ajax({
      type: "POST",
      url: "include/markAsRead.php",
      data: {
        // city: 'tempCustID='+tempCustID+'&cid='+cid
        msgID: msgID,
        cid: cid
      }
    }).done(function(data) {
      $('#' + buttonID).html('Read');
      $('#' + buttonID).attr("disabled", true);
      console.log(data);
      // window.location.reload()
      //$("#response").html(data);
    });

    // alert(a);
  }
</script>

<body>
  <?php include "include/nav.php" ?>

  <div style="padding-top: 50px;" class="container">
    <div class="row">
      <div class="col-sm-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Hood</a>
          <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Block</a>
          <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Friends</a>
          <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">Personal(Friend/Neighbour)</a>
          <span id='userIdMap'><?php echo $_SESSION["id"] ?></span>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="tab-content" id="v-pills-tabContent">
          <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">

            <?php
            $cid = $_SESSION['id'];

            $sql = "select t.thread_id, t.thread_subject, (case when c.cust_id = ?
            then 'You'
            else c.cust_name end) as thread_initiated_by,
                    DATE_FORMAT(initiated_date,'%M %d, %Y') as initiation_day,
                    DATE_FORMAT(initiated_date,'%r') as initiation_time
                    from threads t, customer c
                    where t.initiated_by= c.cust_id
                    and t.recipient_type='hood'
                    and t.initiated_by in (
                    select a.cust_id  from customer a, blocks b
                    where a.block_id=b.block_id
                    and b.hood_id = (select hood_id from customer, blocks 
                    where cust_id=?
                    and customer.block_id=blocks.block_id)) order by initiated_date desc";
            $statement = $link->prepare($sql);
            $statement->bind_param('ii', $cid, $cid);
            if (!$statement->execute()) {
              die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
            } else {
              $result = $statement->get_result();
            }



            $sql2 = "select m.message_id,m.message_body, 
            (case when c.cust_id = ?
            then 'You'
            else c.cust_name end) as message_sender,
            (CASE mb.message_read
            WHEN 'UNREAD' THEN 'Mark as Read'
            ELSE 'Read' end) as message_status,
            DATE_FORMAT(message_date,'%M %d, %Y') as message_sent_day,
              DATE_FORMAT(message_date,'%r') as message_sent_time
            from messages m,message_broadcast mb, customer c
            where m.thread_id=?
            and m.message_id=mb.message_id
            and mb.message_receiver_id=?
            and m.message_author_id=c.cust_id
            order by m.message_date";
            $statement2 = $link->prepare($sql2);


            if ($result->num_rows > 0) {
              while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $tid = $row["thread_id"];
                $thread_title = $row["thread_subject"];
                $thread_initiated_by = $row["thread_initiated_by"];
                $thread_initiated_date = $row["initiation_day"];
                $thread_initiated_time = $row["initiation_time"];
                echo "<div><h3 style='color:#0066CC;'>" . $thread_title . "</h3><p style='color:#0066CC;'><i>" . "Thread Initiated by <b>" . $thread_initiated_by . "</b> on " . $thread_initiated_date . " at " . $thread_initiated_time . "</i></p>";

                $statement2->bind_param('iii', $cid, $tid, $cid);


                if (!$statement2->execute()) {
                  die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
                } else {
                  $result2 = $statement2->get_result();
                }
                while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
                  $message = $row2["message_body"];
                  $message_by = $row2["message_sender"];
                  $message_date = $row2["message_sent_day"];
                  $message_time = $row2["message_sent_time"];
                  $btn_text = $row2["message_status"];
                  $buttonID = "msg" . $row2["message_id"];
                  $msgID = $row2["message_id"];
                  echo "<div><p>" . $message . "</p> <p style='font-size:smaller;'> <i>Sent By <b>" . $message_by . "</b> on " . $message_date . " at " . $message_time . "</i><button type='button' id=" . $buttonID . " class='btn btn-primary btn-sm float-right customBtn' onclick='markAsRead(\"$buttonID\",\"$msgID\",\"$cid\")'>" . $btn_text . "</button></p><hr></div>";
                }
                echo "</div>";
                echo "<form class='form-inline' action='../pro4/include/threadReply.php' method='post'>";
                echo "<div class='form-group flex-fill mr-2'>";
                echo "<input  type='text' name='reply' class='form-control w-100' placeholder='Type Yor Reply' required>";
                echo "<input  type='hidden' name='threadID' value='$tid' required>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary btn-sm float-right'>Reply</button>";
                echo "</form>";
                echo "<div class='border-top my-3'></div>";
              }
              $result->close();
            } else {
              echo "NO THREADS";
            }
            $statement->close();
            // $link->close();
            ?>

          </div>
          <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
            <?php
            $cid = $_SESSION['id'];

            $sql = "select t.thread_id, t.thread_subject, (case when c.cust_id = ?
            then 'You'
            else c.cust_name end) as thread_initiated_by,
                    DATE_FORMAT(initiated_date,'%M %d, %Y') as initiation_day,
                    DATE_FORMAT(initiated_date,'%r') as initiation_time
                    from threads t, customer c
                    where t.initiated_by= c.cust_id
                    and t.recipient_type='block'
                    and t.initiated_by in (
                    select a.cust_id  from customer a
                    where a.block_id=(select block_id from customer 
                      where cust_id=?))
              order by initiated_date desc;";
            $statement = $link->prepare($sql);
            $statement->bind_param('ii', $cid, $cid);
            if (!$statement->execute()) {
              die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
            } else {
              $result = $statement->get_result();
            }



            $sql2 = "select m.message_id,m.message_body, 
            (case when c.cust_id = ?
            then 'You'
            else c.cust_name end) as message_sender,
            (CASE mb.message_read
            WHEN 'UNREAD' THEN 'Mark as Read'
            ELSE 'Read' end) as message_status,
            DATE_FORMAT(message_date,'%M %d, %Y') as message_sent_day,
              DATE_FORMAT(message_date,'%r') as message_sent_time
            from messages m,message_broadcast mb, customer c
            where m.thread_id=?
            and m.message_id=mb.message_id
            and mb.message_receiver_id=?
            and m.message_author_id=c.cust_id
            order by m.message_date";
            $statement2 = $link->prepare($sql2);


            if ($result->num_rows > 0) {
              while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $tid = $row["thread_id"];
                $thread_title = $row["thread_subject"];
                $thread_initiated_by = $row["thread_initiated_by"];
                $thread_initiated_date = $row["initiation_day"];
                $thread_initiated_time = $row["initiation_time"];
                echo "<div><h3 style='color:#0066CC;'>" . $thread_title . "</h3><p style='color:#0066CC;'><i>" . "Thread Initiated by <b>" . $thread_initiated_by . "</b> on " . $thread_initiated_date . " at " . $thread_initiated_time . "</i></p>";


                $statement2->bind_param('iii', $cid, $tid, $cid);


                if (!$statement2->execute()) {
                  die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
                } else {
                  $result2 = $statement2->get_result();
                }
                while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
                  $message = $row2["message_body"];
                  $message_by = $row2["message_sender"];
                  $message_date = $row2["message_sent_day"];
                  $message_time = $row2["message_sent_time"];
                  $btn_text = $row2["message_status"];
                  $buttonID = "msg" . $row2["message_id"];
                  $msgID = $row2["message_id"];
                  echo "<div><p>" . $message . "</p> <p style='font-size:smaller;'> <i>Sent By <b>" . $message_by . "</b> on " . $message_date . " at " . $message_time . "</i><button type='button' id=" . $buttonID . " class='btn btn-primary btn-sm float-right customBtn' onclick='markAsRead(\"$buttonID\",\"$msgID\",\"$cid\")'>" . $btn_text . "</button></p><hr></div>";
                }
                echo "</div>";
                echo "<form class='form-inline' action='../pro4/include/threadReply.php' method='post'>";
                echo "<div class='form-group flex-fill mr-2'>";
                echo "<input  type='text' name='reply' class='form-control w-100' placeholder='Type Yor Reply' required>";
                echo "<input  type='hidden' name='threadID' value='$tid' required>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary btn-sm float-right'>Reply</button>";
                echo "</form>";
                echo "<div class='border-top my-3'></div>";
              }
              $result->close();
            } else {
              echo "NO THREADS";
            }
            $statement->close();
            // $link->close();
            ?>
          </div>
          <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
            <?php
            $cid = $_SESSION['id'];

            $sql = "select t.thread_id, t.thread_subject, (case when c.cust_id = ?
            then 'You'
            else c.cust_name end) as thread_initiated_by,
            DATE_FORMAT(initiated_date,'%M %d, %Y') as initiation_day,
            DATE_FORMAT(initiated_date,'%r') as initiation_time
            from threads t, customer c
            where t.initiated_by= c.cust_id
            and t.recipient_type='allfriends'
            and t.initiated_by in(
                        select received_by_cust as friends from FRIENDS
                        where sent_by_cust = ?
                        and request_status='approved'
                        union
                        select sent_by_cust as friends from FRIENDS
                        where received_by_cust = ?
                        and request_status='approved'
                        union 
                        select ? as friends from dual)
              order by initiated_date desc;";
            $statement = $link->prepare($sql);
            $statement->bind_param('iiii', $cid, $cid, $cid, $cid);
            if (!$statement->execute()) {
              die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
            } else {
              $result = $statement->get_result();
            }



            $sql2 = "select m.message_id,m.message_body, 
            (case when c.cust_id = ?
            then 'You'
            else c.cust_name end) as message_sender,
            (CASE mb.message_read
            WHEN 'UNREAD' THEN 'Mark as Read'
            ELSE 'Read' end) as message_status,
            DATE_FORMAT(message_date,'%M %d, %Y') as message_sent_day,
              DATE_FORMAT(message_date,'%r') as message_sent_time
            from messages m,message_broadcast mb, customer c
            where m.thread_id=?
            and m.message_id=mb.message_id
            and mb.message_receiver_id=?
            and m.message_author_id=c.cust_id
            order by m.message_date";
            $statement2 = $link->prepare($sql2);


            if ($result->num_rows > 0) {
              while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $tid = $row["thread_id"];
                $thread_title = $row["thread_subject"];
                $thread_initiated_by = $row["thread_initiated_by"];
                $thread_initiated_date = $row["initiation_day"];
                $thread_initiated_time = $row["initiation_time"];
                echo "<div><h3 style='color:#0066CC;'>" . $thread_title . "</h3><p style='color:#0066CC;'><i>" . "Thread Initiated by <b>" . $thread_initiated_by . "</b> on " . $thread_initiated_date . " at " . $thread_initiated_time . "</i></p>";


                $statement2->bind_param('iii', $cid, $tid, $cid);


                if (!$statement2->execute()) {
                  die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
                } else {
                  $result2 = $statement2->get_result();
                }
                while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
                  $message = $row2["message_body"];
                  $message_by = $row2["message_sender"];
                  $message_date = $row2["message_sent_day"];
                  $message_time = $row2["message_sent_time"];
                  $btn_text = $row2["message_status"];
                  $buttonID = "msg" . $row2["message_id"];
                  $msgID = $row2["message_id"];
                  echo "<div><p>" . $message . "</p> <p style='font-size:smaller;'> <i>Sent By <b>" . $message_by . "</b> on " . $message_date . " at " . $message_time . "</i><button type='button' id=" . $buttonID . " class='btn btn-primary btn-sm float-right customBtn' onclick='markAsRead(\"$buttonID\",\"$msgID\",\"$cid\")'>" . $btn_text . "</button></p><hr></div>";
                }

                echo "</div>";
                echo "<form class='form-inline' action='../pro4/include/threadReply.php' method='post'>";
                echo "<div class='form-group flex-fill mr-2'>";
                echo "<input  type='text' name='reply' class='form-control w-100' placeholder='Type Yor Reply' required>";
                echo "<input  type='hidden' name='threadID' value='$tid' required>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary btn-sm float-right'>Reply</button>";
                echo "</form>";
                echo "<div class='border-top my-3'></div>";
              }
              $result->close();
            } else {
              echo "NO THREADS";
            }
            $statement->close();
            // $link->close();
            ?>
          </div>
          <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
            <?php
            $cid = $_SESSION['id'];

            $sql = "select t.thread_id, t.thread_subject, 
            (case when c.cust_id = ?
              then 'You'
              else c.cust_name end) as thread_initiated_by,
              (case when t.recipient_user_id=?
              then 'You'
              else (select cust_name from customer d where d.cust_id= t.recipient_user_id) end)
               as thread_directed_to,
            DATE_FORMAT(initiated_date,'%M %d, %Y') as initiation_day,
            DATE_FORMAT(initiated_date,'%r') as initiation_time
            from threads t, customer c
            where t.initiated_by= c.cust_id
            and t.recipient_type in('friend', 'direct_neighbour')
            and (t.recipient_user_id = ? or t.initiated_by= ?)
              order by initiated_date desc";
            $statement = $link->prepare($sql);
            $statement->bind_param('iiii', $cid, $cid, $cid, $cid);
            if (!$statement->execute()) {
              die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
            } else {
              $result = $statement->get_result();
            }



            $sql2 = "select m.message_id,m.message_body,(case when c.cust_id = ?
            then 'You'
            else c.cust_name end) as message_sender, 
            (CASE mb.message_read
            WHEN 'UNREAD' THEN 'Mark as Read'
            ELSE 'Read' end) as message_status,
            DATE_FORMAT(message_date,'%M %d, %Y') as message_sent_day,
              DATE_FORMAT(message_date,'%r') as message_sent_time
            from messages m,message_broadcast mb, customer c
            where m.thread_id=?
            and m.message_id=mb.message_id
            and mb.message_receiver_id=?
            and m.message_author_id=c.cust_id
            order by m.message_date";
            $statement2 = $link->prepare($sql2);


            if ($result->num_rows > 0) {
              while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $tid = $row["thread_id"];
                $thread_title = $row["thread_subject"];
                $thread_initiated_by = $row["thread_initiated_by"];
                $thread_initiated_date = $row["initiation_day"];
                $thread_initiated_time = $row["initiation_time"];
                $thread_directed_to = $row["thread_directed_to"];
                echo "<div><h3 style='color:#0066CC;'>" . $thread_title . "</h3><p style='color:#0066CC;'><i>" . "Thread Initiated by <b>" . $thread_initiated_by . "</b> to <b>" . $thread_directed_to . " </b>on " . $thread_initiated_date . " at " . $thread_initiated_time . "</i></p>";


                $statement2->bind_param('iii', $cid, $tid, $cid);


                if (!$statement2->execute()) {
                  die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
                } else {
                  $result2 = $statement2->get_result();
                }
                while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
                  $message = $row2["message_body"];
                  $message_by = $row2["message_sender"];
                  $message_date = $row2["message_sent_day"];
                  $message_time = $row2["message_sent_time"];
                  $btn_text = $row2["message_status"];
                  $buttonID = "msg" . $row2["message_id"];
                  $msgID = $row2["message_id"];
                  echo "<div><p>" . $message . "</p> <p style='font-size:smaller;'> <i>Sent By <b>" . $message_by . "</b> on " . $message_date . " at " . $message_time . "</i><button type='button' id=" . $buttonID . " class='btn btn-primary btn-sm float-right customBtn' onclick='markAsRead(\"$buttonID\",\"$msgID\",\"$cid\")'>" . $btn_text . "</button></p><hr></div>";
                }

                echo "</div>";
                echo "<form class='form-inline' action='../pro4/include/threadReply.php' method='post'>";
                echo "<div class='form-group flex-fill mr-2'>";
                echo "<input  type='text' name='reply' class='form-control w-100' placeholder='Type Yor Reply' required>";
                echo "<input  type='hidden' name='threadID' value='$tid' required>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary btn-sm float-right'>Reply</button>";
                echo "</form>";
                echo "<div class='border-top my-3'></div>";
              }
              $result->close();
            } else {
              echo "NO THREADS";
            }
            $statement->close();
            // $link->close();
            ?>
          </div>
        </div>
      </div>
      <div class="col-sm-3">
        <h4>Your Neighbourhood</h4>
        <div style="height: 300px; width: 300px;" id="map"></div>

        <script type="text/javascript">
          // $(document).ready(function() {
          //       //debugger;
          //       var custID = $("#userIdMap").text();
          //       $("#userIdMap").hide()
          //       $.ajax({
          //         type: "POST",
          //         url: "include/mapMarkers.php",
          //         data: {
          //           cid: custID
          //         }
          //       }).done(function(data) {
          //         debugger;
          //         console.log(data);
          //         var coordinates = data;
          //         var coordinatesArr = coordinates.split('%%');
          //         myLatLng = {
          //           lat: parseFloat(coordinatesArr[1]),
          //           lng: parseFloat(coordinatesArr[2])
          //         };
          //         v_title=coordinatesArr[3];
          //         initMap();

          //       });
          //     });



          function initMap() {
            var latitude = 42.0941; // YOUR LATITUDE VALUE
            var longitude = -92.14391; // YOUR LONGITUDE VALUE


            //console.log(myLatLng);
            // alert(1);
            //alert(myLatLng.lat);
            var custID = $("#userIdMap").text();
            $("#userIdMap").hide()
            $.ajax({
              type: "POST",
              url: "include/mapMarkers.php",
              data: {
                cid: custID
              }
            }).done(function(data) {
              debugger;
              console.log(data);
              if (data != '') {
                var dataStr = data;
                var dataArr = dataStr.split('^^');
                var logged_in_cust_coordinates = dataArr[1].split('&&');
                var block_details = dataArr[0].split('%%');
                var custLatLng = {
                  lat: parseFloat(logged_in_cust_coordinates[0]),
                  lng: parseFloat(logged_in_cust_coordinates[1])
                };
                var map = new google.maps.Map(document.getElementById('map'), {
                  center: custLatLng,
                  zoom: 2
                });

                for (var i = 0; i < block_details.length; i++) {
                  var coordinates = block_details[i];
                  var coordinatesArr = coordinates.split('$$');
                  var myLatLng = {
                    lat: parseFloat(coordinatesArr[2]),
                    lng: parseFloat(coordinatesArr[3])
                  };
                  var v_title = '# of People in ' + coordinatesArr[1] + ' : ' + coordinatesArr[0];
                  var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    title: v_title
                  });
                  // google.maps.event.addDomListener(marker, 'click', function(a) {
                  //     window.location.href = 'http://www.google.co.uk/';
                  // });
                }
              }
              else
              {
                var custLatLng = {
                  lat: 40.730610,
                  lng: -73.935242
                };
                var map = new google.maps.Map(document.getElementById('map'), {
                  center: custLatLng,
                  zoom: 10
                });
              }
            });
          }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApCWOHmn53M9sVFahUSi0D_dyEiJHDSbw&callback=initMap" async defer></script>
        <!-- <br>243 68th st, Brooklyn,<br>
        NY 11220<br><br>
        No. of people in same block = <br>
        No. of people in same hood = -->
      </div>

    </div>
  </div>




  <?php include "include/footer.php" ?>