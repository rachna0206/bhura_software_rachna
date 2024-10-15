<?php
include "db_connect.php";
$obj = new DB_connect();

// $ind_estate = isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "";
// $company = isset($_REQUEST['company']) ? $_REQUEST['company'] : "";
// $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
// $operation = isset($_REQUEST['operation']) ? $_REQUEST['operation'] : "";
// $date_time = isset($_REQUEST['date_time']) ? $_REQUEST['date_time'] : "";


// Construct the WHERE clause based on the filters
// $ind_estate_str = ($ind_estate != "") ? " AND lower(industrial_estate) = '" . strtolower($ind_estate) . "'" : "";
// $cookie_str = isset($_COOKIE['report_estate_id']) && !empty($_COOKIE['report_estate_id']) ? " AND id = '" . $_COOKIE['report_estate_id'] . "'" : "";
// $company_str = ($scompany != "") ? " AND lower(company) = '" . strtolower($company) . "'" : "";
// $name_str = ($city != "") ? " AND lower(name) = '" . strtolower($name) . "'" : "";
// $operation_str = ($operation != "") ? " AND lower(operation) = '" . strtolower($operation) . "'" : "";
// $date_time_str = ($date_time != "") ? " AND lower(date_time) = '" . strtolower($date_time) . "'" : "";



// Complete query with filters
$query = "SELECT * FROM pr_user_activity LEFT JOIN tbl_users ON user_id = tbl_users.id LEFT JOIN tbl_company ON company_id = tbl_company.id LEFT JOIN tbl_industrial_estate ON industrial_estate_id = tbl_industrial_estate.id";
$stmt_list = $obj->con1->prepare($query);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();



// file name for download
$filename = "User Report.csv";
// Create a file pointer 
$f = fopen('php://memory', 'w');
// Set column headers 
$fields = array('ID', 'Industrial Estate', 'Company', 'Username', 'Operation', 'Date Time');
$delimiter = ",";
fputcsv($f, $fields, $delimiter);

$i = 1;
while ($row0 = mysqli_fetch_array($result)) {

    $lineData = array($row0['id'], $row0['industrial_estate'], $row0['company'], $row0['name'], $row0['operation'], $row0['date_time']);
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