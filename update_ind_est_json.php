<?php
  include("header.php");

$user_id = $_SESSION["id"];

$industrial_estate=strtolower("silvet textile hub");
$new_est_name=strtoupper("silver textile hub");
$taluka="mangrol";
$area="pipodara";
//check plot/estate entry in tdrawdata
$stmt_plot =  $obj->con1->prepare("SELECT * FROM tbl_tdrawdata WHERE lower(raw_data->'$.post_fields.Taluka') like '%" . strtolower($taluka) . "%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%" . strtolower($industrial_estate) . "%' and lower(raw_data->'$.post_fields.Area') like '%" . strtolower($area) . "%'");
$stmt_plot->execute();
$plot_res = $stmt_plot->get_result();
$stmt_plot->close();

while($estate = mysqli_fetch_array($plot_res))
{
// Decode JSON into a PHP associative array
$data = json_decode($estate["raw_data"]);

$data->post_fields->IndustrialEstate = $new_est_name;

// Encode the updated data back into JSON
$updatedJsonData = json_encode($data, JSON_UNESCAPED_UNICODE);

echo "<br>" . $updatedJsonData;

    //Update the database with the modified JSON
    $update_stmt = $obj->con1->prepare("UPDATE tbl_tdrawdata SET raw_data = ? WHERE id = ?");
    $update_stmt->bind_param("si", $updatedJsonData, $estate["id"]);
    $update_stmt->execute();
    $update_stmt->close();
}

include("footer.php");
?>