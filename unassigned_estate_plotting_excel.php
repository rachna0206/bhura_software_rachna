<?php
include "db_connect.php";
$obj = new DB_connect();

$state = isset($_REQUEST['state_id']) ? $_REQUEST['state_id'] : "";
$city = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : "";
$taluka = isset($_REQUEST['taluka']) ? $_REQUEST['taluka'] : "";
$area = isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : "";
$ind_estate = isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "";

// Construct the WHERE clause based on the filters
$state_str = ($state != "") ? " AND lower(state_id) = '" . strtolower($state) . "'" : "";
$city_str = ($city != "") ? " AND lower(city_id) = '" . strtolower($city) . "'" : "";
$taluka_str = ($taluka != "") ? " AND lower(taluka) = '" . strtolower($taluka) . "'" : "";
$area_str = ($area != "") ? " AND lower(area_id) = '" . strtolower($area) . "'" : "";
$ind_estate_str = ($ind_estate != "") ? " AND lower(industrial_estate) = '" . strtolower($ind_estate) . "'" : "";
$cookie_str = isset($_COOKIE['report_estate_id']) && !empty($_COOKIE['report_estate_id']) ? " AND id = '" . $_COOKIE['report_estate_id'] . "'" : "";


// Complete query with filters
$query = "SELECT * 
          FROM tbl_industrial_estate 
          WHERE state_id = 'GUJARAT' 
          AND city_id = 'SURAT' 
          AND id NOT IN (SELECT industrial_estate_id FROM pr_add_industrialestate_details)
          AND id NOT IN (SELECT industrial_estate_id FROM assign_estate WHERE action = 'estate_plotting')
          " . $state_str . $city_str . $taluka_str . $area_str . $ind_estate_str . $cookie_str . "
          ORDER BY id";

$stmt_list = $obj->con1->prepare($query);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();



// file name for download
$filename = "Unassigned Estate.csv";
// Create a file pointer 
$f = fopen('php://memory', 'w');
// Set column headers 
$fields = array('ID', 'State', 'City', 'Taluka', 'Area', 'Industrial Estate');
$delimiter = ",";
fputcsv($f, $fields, $delimiter);

$i = 1;
while ($row0 = mysqli_fetch_array($result)) {

  $lineData = array($row0['id'], $row0['state_id'], $row0['city_id'], $row0['taluka'], $row0['area_id'], $row0['industrial_estate']);
  fputcsv($f, $lineData, $delimiter);


  $i++;

}


// Move back to beginning of file 
fseek($f, 0);

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/csv");

//output all remaining data on a file pointer 
fpassthru($f);

?>