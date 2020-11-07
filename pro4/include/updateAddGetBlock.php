<?php
include("../config.php");
session_start();
if (isset($_POST["city"])) {
    // Capture selected city
    $loggedInUserID = $_SESSION["id"];
    $selectedCity = $_POST["city"];
    $sql = "  select distinct block_id , block_name from blocks where city_id=?
    and block_id not in (select block_id from customer where cust_id=?
 and city_id=?
 and active_status='active')
     order by block_name";
    $statement = $link->prepare($sql);
        $statement->bind_param('iii', $selectedCity,$loggedInUserID,$selectedCity);
        $statement->execute();
        $result = $statement->get_result();
    if ($result->num_rows > 0 && $selectedCity !== 'Select') {

        echo "<div class='form-group'>   
                <label>Block</label>
                <select name='block' id='blockID' class='form-control' required>
                <option value='Select'>Select</option>
                ";
        // output data of each row
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            echo "<option value=" . $row["block_id"] . ">" . $row["block_name"] . "</option>";
        }
       // $result->close();
        echo "</select> </div>";
    } else {
        echo "<div class='form-group'>   
                <label>Block</label>
                <select name='block' id='blockID' class='form-control' required>
                <option value='Select'>Select</option>
                </select> </div>
                ";
    }
    $statement->close();



    // Define country and city array
    // $countryArr = array(
    //                 "1" => array("New Yourk", "Los Angeles", "California"),
    //                 "2" => array("Mumbai", "New Delhi", "Bangalore"),
    //                 "uk" => array("London", "Manchester", "Liverpool")
    //             );

    // // Display city dropdown based on country name
    // if($country !== 'Select'){
    //     echo "<label>City:</label>";
    //     echo "<select>";
    //     foreach($countryArr[$country] as $value){
    //         echo "<option>". $value . "</option>";
    //     }
    //     echo "</select>";
    // } 
}

//$link->close();
