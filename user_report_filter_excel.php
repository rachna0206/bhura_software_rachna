<?php

include("db_connect.php");

$obj = new DB_connect();
// tbl_user - user
// tbl_industrial_estate
// pr_user_Activity 
// tbl_/pr_company

// $stmt_list = $obj->con1->prepare("SELECT cid, industrial_estate, area, taluka, company_id, sum(count) as total_count FROM pr_visit_count group by company_id");

$query = "SELECT * FROM pr_user_activity p1 LEFT JOIN tbl_users ON user_id = tbl_users.id LEFT JOIN tbl_company ON company_id = tbl_company.id LEFT JOIN tbl_industrial_estate ON industrial_estate_id = tbl_industrial_estate.id WHERE 1=1";
$param_str = "";
$params = [];

if ($_REQUEST["start_date"] != "") {
    // echo 1;
    $query .= " AND DATE_FORMAT(p1.date_time, '%Y-%m-%d') >= ?";
    $param_str .= "s";
    array_push($params, $_REQUEST["start_date"]);
}

if ($_REQUEST["end_date"] != "") {
    // echo 2;
    $query .= " AND DATE_FORMAT(p1.date_time, '%Y-%m-%d') <= ?";
    $param_str .= "s";
    array_push($params, $_REQUEST["end_date"]);
}

if ($_REQUEST["select_user_id"] != "") {
    // echo 3;
    $query .= " AND p1.user_id = ?";
    $param_str .= "s";
    array_push($params, $_REQUEST["select_user_id"]);
}

// if ($_REQUEST["start_date"] != "" && $_REQUEST["end_date"] != "" && $_REQUEST["select_user_id"] != "") { // 111
//     // echo 4;
//     $stmt_list = $obj->con1->prepare("SELECT * FROM pr_user_activity p1 LEFT JOIN tbl_users ON user_id = tbl_users.id LEFT JOIN tbl_company ON company_id = tbl_company.id LEFT JOIN tbl_industrial_estate ON industrial_estate_id = tbl_industrial_estate.id WHERE (DATE_FORMAT(p1.date_time, '%Y-%m-%d') >= ? AND DATE_FORMAT(p1.date_time, '%Y-%m-%d') <= ?) AND p1.user_id = ?");
//     $stmt_list->bind_param("sss", $_REQUEST["start_date"], $_REQUEST["end_date"], $_REQUEST["select_user_id"]);
// }

// if ($_REQUEST["start_date"] != "" && $_REQUEST["end_date"] == "" && $_REQUEST["select_user_id"] != "") { // 101
//     // echo 5;
//     $stmt_list = $obj->con1->prepare("SELECT * FROM pr_user_activity p1 LEFT JOIN tbl_users ON user_id = tbl_users.id LEFT JOIN tbl_company ON company_id = tbl_company.id LEFT JOIN tbl_industrial_estate ON industrial_estate_id = tbl_industrial_estate.id WHERE DATE_FORMAT(p1.date_time, '%Y-%m-%d') = ? AND p1.user_id = ?");
//     $stmt_list->bind_param("ss", $_REQUEST["start_date"], $_REQUEST["select_user_id"]);
// }

// if ($_REQUEST["start_date"] == "" && $_REQUEST["end_date"] != "" && $_REQUEST["select_user_id"] == "") { // 010
//     // echo 6;
//     $stmt_list = $obj->con1->prepare("SELECT * FROM pr_user_activity p1 LEFT JOIN tbl_users ON user_id = tbl_users.id LEFT JOIN tbl_company ON company_id = tbl_company.id LEFT JOIN tbl_industrial_estate ON industrial_estate_id = tbl_industrial_estate.id WHERE DATE_FORMAT(p1.date_time, '%Y-%m-%d') <= ?");
//     $stmt_list->bind_param("s", $_REQUEST["end_date"]);
// }

$stmt_list = $obj->con1->prepare($query);
$stmt_list->bind_param($param_str, ...$params);

// $stmt_list->bind_param("sss", $_REQUEST["start_date"], $_REQUEST["end_date"], $_REQUEST["select_user_id"]);
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