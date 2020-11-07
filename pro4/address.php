<?php include "include/header.php" ?>
<script type="text/javascript">
  $(document).ready(function() {
    $("#cityID").change(function() {
      debugger;
      var selectedCity = $("#cityID option:selected").val();
      $.ajax({
        type: "POST",
        url: "include/updateAddGetBlock.php",
        data: {
          city: selectedCity
        }
      }).done(function(data) {
        debugger;
        console.log(data);
        $("#response1").html(data);
      });
    });
  });
</script>

<body>
  <?php include "include/nav.php" ?>
  <div class="alert alert-danger text-center" role="alert">
  <b>NOTE:</b> ON UPDATION OF YOUR ADDRESS,YOUR CURRENT PROFILE WILL BE DISABLED AND YOU'LL BE ABLE TO SIGN IN WHEN YOU ARE APPROVED BY NEW MEMBERS
  </div>
  <div style="width: 350px; padding: 20px;" class="container">
    <form action="../pro4/include/addrUpdate.php" method="POST">
      <?php

      $sql = "select distinct city_id, city_name from blocks";
      $stmt1 = $link->prepare($sql);
      $stmt1->execute();
      $result = $stmt1->get_result();
      $result = $link->query($sql);
      if ($result->num_rows > 0) {
        echo "<div class='form-group'>   
                <label>City</label>
                <select name='city' id='cityID' class='form-control' required>
                <option value='Select'>Select</option>";
        // output data of each row
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          echo "<option value=" . $row["city_id"] . ">" . $row["city_name"] . "</option>";
        }
        echo "</select> </div>";
      }
      ?>
      <div id="response1" class='form-group'>
        <label>Block</label>
        <select name="block" id="blockID" class="form-control" required>

        </select>
        <!--Response will be inserted here-->
      </div>


      <div class="form-group">
        <label>Apartment Number</label>
        <input type="text" name="apt" class="form-control" required>
      </div>


      <button type="submit" class="btn btn-primary">Update</button>



    </form>

  </div>

  <?php include "include/footer.php" ?>