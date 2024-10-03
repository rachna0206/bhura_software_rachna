<?php
include "db_connect.php";
$obj = new DB_connect();

$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
$city = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : "";
$taluka = isset($_REQUEST['taluka']) ? $_REQUEST['taluka'] : "";
$area = isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : "";
$ind_estate = isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "";
$start_date = isset($_REQUEST['start_dt']) ? $_REQUEST['start_dt'] : "";
$end_date = isset($_REQUEST['end_dt']) ? $_REQUEST['end_dt'] : "";

// Construct the WHERE clause based on the filters
$name_str = ($name != "") ? " AND lower(name) = '" . strtolower($name) . "'" : "";
$city_str = ($city != "") ? " AND lower(city_id) = '" . strtolower($city) . "'" : "";
$taluka_str = ($taluka != "") ? " AND lower(taluka) = '" . strtolower($taluka) . "'" : "";
$area_str = ($area != "") ? " AND lower(area_id) = '" . strtolower($area) . "'" : "";
$ind_estate_str = ($ind_estate != "") ? " AND lower(industrial_estate) = '" . strtolower($ind_estate) . "'" : "";
$cookie_str = isset($_COOKIE['report_estate_id']) && !empty($_COOKIE['report_estate_id']) ? " AND id = '" . $_COOKIE['report_estate_id'] . "'" : "";
$start_date_str = ($start_date != "") ? " AND lower(start_dt) = '" . strtolower($start_date) . "'" : "";
$end_date_str = ($end_date != "") ? " AND lower(end_dt) = '" . strtolower($end_date) . "'" : "";

// Complete query with filters
$query = "SELECT a1.*, u1.name, i1.state_id, i1.city_id, i1.taluka, i1.area_id, i1.industrial_estate 
          FROM assign_estate a1
          JOIN tbl_users u1 ON a1.employee_id = u1.id
          JOIN tbl_industrial_estate i1 ON a1.industrial_estate_id = i1.id 
          WHERE a1.action = 'estate_plotting' 
          AND a1.industrial_estate_id NOT IN (
              SELECT industrial_estate_id 
              FROM pr_add_industrialestate_details 
              WHERE plotting_pattern IS NOT NULL OR status != 'Verified'
          ) 
          $name_str $city_str $taluka_str $area_str $ind_estate_str $cookie_str $start_date_str $end_date_str
          ORDER BY a1.id DESC";


$stmt_list = $obj->con1->prepare($query);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();



// file name for download
$filename = "Assigned Estate.csv";
// Create a file pointer 
$f = fopen('php://memory', 'w');
// Set column headers 
$fields = array('ID', 'Name', 'City', 'Taluka', 'Area', 'Industrial Estate','Start Date','End Date');
$delimiter = ",";
fputcsv($f, $fields, $delimiter);

$i = 1;
while ($row0 = mysqli_fetch_array($result)) {

  $lineData = array($row0['id'], $row0['name'], $row0['city_id'], $row0['taluka'], $row0['area_id'], $row0['industrial_estate'],$row0['start_dt'],$row0['end_dt']);
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