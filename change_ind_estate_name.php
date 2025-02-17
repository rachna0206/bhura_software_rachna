<?php
include("header.php");

// State List
$stmt_state_list = $obj->con1->prepare("select DISTINCT(state) from all_taluka where state='GUJARAT'");
$stmt_state_list->execute();
$state_result = $stmt_state_list->get_result();
$stmt_state_list->close();

// City List
$stmt_city = $obj->con1->prepare("select DISTINCT(district) from all_taluka where state='GUJARAT' and district='SURAT'");
$stmt_city->execute();
$city_result = $stmt_city->get_result();
$stmt_city->close();

// Taluka List
$stmt_taluka = $obj->con1->prepare("select DISTINCT(subdistrict) from all_taluka where state='GUJARAT' and district='SURAT'");
$stmt_taluka->execute();
$taluka_result = $stmt_taluka->get_result();
$stmt_taluka->close();

if (isset($_REQUEST["btnsubmit"])) {



    $user_id = $_SESSION["id"];
    $old_name = $_REQUEST["old_name"];
    $new_name = $_REQUEST["new_name"];
    $industrial_estate = strtolower($old_name);
    $new_est_name = strtoupper($new_name);
    $taluka = $_REQUEST["taluka"];
    $area = $_REQUEST["area"];

    //check plot/estate entry in tdrawdata

    //echo "SELECT * FROM tbl_tdrawdata WHERE lower(raw_data->'$.post_fields.Taluka') like '%" . strtolower($taluka) . "%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%" . strtolower($industrial_estate) . "%' and lower(raw_data->'$.post_fields.Area') like '%" . strtolower($area) . "%'";

    $stmt_plot =  $obj->con1->prepare("SELECT * FROM tbl_tdrawdata WHERE lower(raw_data->'$.post_fields.Taluka') like '%" . strtolower($taluka) . "%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%" . strtolower($industrial_estate) . "%' and lower(raw_data->'$.post_fields.Area') like '%" . strtolower($area) . "%'");
    $stmt_plot->execute();
    $plot_res = $stmt_plot->get_result();
    $stmt_plot->close();

    while ($estate = mysqli_fetch_array($plot_res)) {
        // Decode JSON into a PHP associative array
        $data = json_decode($estate["raw_data"]);

        $data->post_fields->IndustrialEstate = $new_est_name;

        // Encode the updated data back into JSON
        $updatedJsonData = json_encode($data, JSON_UNESCAPED_UNICODE);

        echo "<br>" . $updatedJsonData;

        //Update the database with the modified JSON

        echo "UPDATE tbl_tdrawdata SET raw_data = '$updatedJsonData' WHERE id = '".$estate["id"]."'";
        $update_stmt = $obj->con1->prepare("UPDATE tbl_tdrawdata SET raw_data = ? WHERE id = ?");
        $update_stmt->bind_param("si", $updatedJsonData, $estate["id"]);
        $update_stmt->execute();
        $update_stmt->close();
        if(mysqli_affected_rows($obj->con1)>0){

            setcookie("msg", "update",time()+3600,"/");
           // header("location:change_ind_estate_name.php");
        }
        else{
            setcookie("msg", "fail",time()+3600,"/");
           // header("location:change_ind_estate_name.php");
        }
    }
}

?>


<h4 class="fw-bold py-3 mb-4">Update Industrial Estate Name</h4>

<?php
if (isset($_COOKIE["msg"])) {

    if ($_COOKIE['msg'] == "data") {

?>
        <div class="alert alert-primary alert-dismissible" role="alert">
            Data added succesfully
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
        <script type="text/javascript">
            eraseCookie("msg")
        </script>
    <?php
    }
    if ($_COOKIE['msg'] == "update") {

    ?>
        <div class="alert alert-primary alert-dismissible" role="alert">
            Data updated succesfully
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
        <script type="text/javascript">
            eraseCookie("msg")
        </script>
    <?php
    }
    if ($_COOKIE['msg'] == "data_del") {

    ?>
        <div class="alert alert-primary alert-dismissible" role="alert">
            Data deleted succesfully
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
        <script type="text/javascript">
            eraseCookie("msg")
        </script>
    <?php
    }
    if ($_COOKIE['msg'] == "fail") {
    ?>

        <div class="alert alert-danger alert-dismissible" role="alert">
            An error occured! Try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
        <script type="text/javascript">
            eraseCookie("msg")
        </script>
    <?php
    }
}
if (isset($_COOKIE["sql_error"])) {
    ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <?php echo urldecode($_COOKIE['sql_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>

    <script type="text/javascript">
        eraseCookie("sql_error")
    </script>
<?php
}
?>

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">

            <div class="card-body">
                <form method="post" enctype="multipart/form-data">

                    <input type="hidden" name="ind_estate_id" id="ind_estate_id">




                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">State</label>
                        <select name="state" id="state" class="form-control" required>
                            <!--    <option value="">Select State</option>  -->
                            <?php while ($state_list = mysqli_fetch_array($state_result)) { ?>
                                <option value="<?php echo $state_list["state"] ?>" selected><?php echo $state_list["state"] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">City</label>
                        <select name="city" id="city" class="form-control" required>
                            <option value="">Select City</option>
                            <?php while ($city_list = mysqli_fetch_array($city_result)) { ?>
                                <option value="<?php echo $city_list["district"] ?>" selected><?php echo $city_list["district"] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Taluka</label>
                        <select name="taluka" id="taluka" onchange="areaList_ade(this.value,city.value,state.value)" class="form-control" required>
                            <option value="">Select Taluka</option>
                            <?php while ($taluka_list = mysqli_fetch_array($taluka_result)) { ?>
                                <option value="<?php echo $taluka_list["subdistrict"] ?>"><?php echo $taluka_list["subdistrict"] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Area</label>
                        <select name="area" id="area" class="form-control" required>
                            <option value="">Select Area</option>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Old Name</label>
                        <input type="text" class="form-control" name="old_name" id="old_name" required />

                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">New Name</label>
                        <input type="text" class="form-control" name="new_name" id="new_name" required />

                    </div>




                    <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>

                    <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>

                </form>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript">
    function areaList_ade(taluka, city, state) {
        $.ajax({
            async: true,
            type: "POST",
            url: "ajaxdata.php?action=areaList_ade",
            data: "taluka=" + taluka + "&city=" + city + "&state_name=" + state,
            cache: false,
            success: function(result) {
                $('#area').html('');
                $('#area').append(result);
            }
        });
    }
</script>

<?php
include("footer.php");
?>