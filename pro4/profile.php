<?php include "include/header.php" ?>

<body>
    <?php include "include/nav.php" ?>

    <div style="padding-top: 50px;" class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Update Picture</a>
                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Update Intro</a>
                    <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Update Password</a>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <div class="form-group">
                                <label>Upload a picture</label>
                                <input type="file" name="pic" accept="image/*" class="form-control-file" id="exampleFormControlFile1" required><br><br>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST["pic"])) {
                            $cid = $_SESSION['id'];
                            $pic = $_POST['pic'];

                            $sql = "update customer set cust_photo=?
                                    where cust_id=?;";
                            $statement = $link->prepare($sql);
                            $statement->bind_param('bi', $pic, $cid);
                            if (!$statement->execute()) {
                                die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
                            } else {
                                echo "Successfully Updated Picture";
                            }
                        }
                    }
                    ?>
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">

                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="form1">
                            <div class="form-group">
                                <textarea name="intro" rows="4" cols="50" id="exampleFormControlTextarea1 form1" placeholder="Please enter intro" required></textarea>
                                <br><br><button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST["intro"])) {
                            $cid = $_SESSION['id'];
                            $intro = $_POST['intro'];

                            $sql = "update customer set cust_intro=? where cust_id=?";
                            $statement = $link->prepare($sql);
                            $statement->bind_param('si', $intro, $cid);
                            if (!$statement->execute()) {
                                die("CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
                            } else {
                                echo "Successfully Updated Intro";
                            }
                        }
                    }
                    ?>
                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                        <form>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control"><br>
                                <button type="button" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
          $cid = $_SESSION['id'];
          $sql = "select cust_name,cust_email,cust_intro,apt_num, h.hood_name,b.city_name,b.block_name
          from customer c, blocks b, hoods h
          where c.block_id=b.block_id
          and c.city_id=b.city_id
          and h.hood_id=b.hood_id
          and c.cust_id=?";
          $statement = $link->prepare($sql);
          $statement->bind_param('i', $cid);
          if (!$statement->execute()) {
            die( "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
          $result = $statement->get_result();
          //  $result = $link->query($sql);
          if ($result->num_rows > 0) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
                $cname = $row['cust_name'];
                $cemail = $row['cust_email'];
                $cintro = $row['cust_intro'];
                $capt = $row['apt_num'];
                $cblock = $row['block_name'];
                $ccity = $row['city_name'];
                $chood = $row['hood_name'];
            }
         ?>
            <div class="col-sm-3">
                <div class="card" style="width: 18rem;">
                    <img class="card-img-top .img-thumbnail" src="img/profile.png" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $cname ?></h5>
                        <p class="card-text"><b>Introduction: </b><?php echo $cintro ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><b>Email: </b><?php echo $cemail ?></li>
                        <li class="list-group-item"><b>Apt Num: </b><?php echo $capt ?></li>
                        <li class="list-group-item"><b>Block: </b><?php echo $cblock ?></li>
                        <li class="list-group-item"><b>Hood: </b><?php echo $chood ?></li>
                        <li class="list-group-item"><b>City: </b><?php echo $ccity ?></li>
                    </ul>
                    <!-- <div class="card-body">
                        <a href="#" class="card-link">Card link</a>
                        <a href="#" class="card-link">Another link</a>
                    </div> -->
                </div>
            </div>

            

        </div>
    </div>


    <?php include "include/footer.php" ?>