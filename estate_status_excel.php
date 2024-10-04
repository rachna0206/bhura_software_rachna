<?php
include "db_connect.php";
$obj = new DB_connect();

$state = isset($_REQUEST['state_id']) ? $_REQUEST['state_id'] : "";
$city = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : "";
$taluka = isset($_REQUEST['taluka']) ? $_REQUEST['taluka'] : "";
$area = isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : "";
$ind_estate = isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "";
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : "";
$added_by = isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : "";
$date_time = isset($_REQUEST['datetime']) ? $_REQUEST['datetime'] : "";

// Construct the WHERE clause based on the filters
$state_str = ($state != "") ? " AND lower(i1.state_id) = '" . strtolower($state) . "'" : "";
$city_str = ($city != "") ? " AND lower(i1.city_id) = '" . strtolower($city) . "'" : "";
$taluka_str = ($taluka != "") ? " AND lower(i1.taluka) = '" . strtolower($taluka) . "'" : "";
$area_str = ($area != "") ? " AND lower(i1.area_id) = '" . strtolower($area) . "'" : "";
$ind_estate_str = ($ind_estate != "") ? " AND lower(i1.industrial_estate) = '" . strtolower($ind_estate) . "'" : "";
$cookie_str = isset($_COOKIE['report_estate_id']) && !empty($_COOKIE['report_estate_id']) ? " AND i1.id = '" . $_COOKIE['report_estate_id'] . "'" : "";
$status_str = ($status != "") ? " AND lower(d1.status) = '" . strtolower($status) . "'" : "";
$added_by_str = ($added_by != "") ? " AND lower(u1.name) = '" . strtolower($added_by) . "'" : "";
$date_time_str = ($date_time != "") ? " AND lower(d1.datetime) = '" . strtolower($date_time) . "'" : "";

// Complete query with filters
$query = "SELECT i1.*, d1.status, u1.name as user_name, d1.datetime 
          FROM `tbl_industrial_estate` i1 
          JOIN `pr_add_industrialestate_details` d1 ON i1.id = d1.industrial_estate_id 
          JOIN tbl_users u1 ON d1.user_id = u1.id 
          WHERE d1.status IN ('Fake', 'Duplicate') 
          $state_str $city_str $taluka_str $area_str $ind_estate_str $cookie_str $status $added_by_str $date_time_str 
          GROUP BY i1.id 
          ORDER BY d1.id DESC";

$stmt_list = $obj->con1->prepare($query);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();

// file name for download
$filename = "Estate Status.csv";
// Create a file pointer 
$f = fopen('php://memory', 'w');
// Set column headers 
$fields = array('ID', 'State', 'City', 'Taluka', 'Area', 'Industrial Estate', 'Status', 'Added By', 'Date Time');
$delimiter = ",";
fputcsv($f, $fields, $delimiter);

while ($row0 = mysqli_fetch_array($result)) {
    $lineData = array(
        $row0['id'],
        $row0['state_id'],
        $row0['city_id'],
        $row0['taluka'],
        $row0['area_id'],
        $row0['industrial_estate'],
        $row0['status'],
        $row0['user_name'],
        $row0['datetime']
    );
    fputcsv($f, $lineData, $delimiter);
}

// Move back to the beginning of the file
fseek($f, 0);

// Output headers for download
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/csv");

// Output all remaining data on a file pointer 
fpassthru($f);
?>