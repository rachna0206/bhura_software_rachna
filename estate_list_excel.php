<?php
include "db_connect.php";
$obj = new DB_connect();

$state = isset($_REQUEST['state_id']) ? $_REQUEST['state_id'] : "";
$city = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : "";
$taluka = isset($_REQUEST['taluka']) ? $_REQUEST['taluka'] : "";
$area = isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : "";
$ind_estate = isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "";

$state_str = ($state != "") ? " AND LOWER(i1.state_id) = '" . strtolower($state) . "'" : "";
$city_str = ($city != "") ? " AND LOWER(i1.city_id) = '" . strtolower($city) . "'" : "";
$taluka_str = ($taluka != "") ? " AND LOWER(i1.taluka) = '" . strtolower($taluka) . "'" : "";
$area_str = ($area != "") ? " AND LOWER(i1.area_id) = '" . strtolower($area) . "'" : "";
$ind_estate_str = ($ind_estate != "") ? " AND LOWER(i1.industrial_estate) = '" . strtolower($ind_estate) . "'" : "";
$cookie_str = (isset($_COOKIE['report_estate_id']) && !empty($_COOKIE['report_estate_id'])) ? " AND i1.id = '" . $_COOKIE['report_estate_id'] . "'" : "";

$query = "SELECT i1.* 
          FROM tbl_industrial_estate i1
          JOIN (SELECT DISTINCT(industrial_estate_id) as estate_id 
                FROM pr_company_plots p1) tbl1 
          ON i1.id = tbl1.estate_id 
          WHERE 1=1 
          $state_str $city_str $taluka_str $area_str $ind_estate_str $cookie_str 
          ORDER BY i1.state_id, i1.city_id, i1.taluka, i1.area_id, i1.industrial_estate";

$stmt_list = $obj->con1->prepare($query);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();

$filename = "Estate List.csv";

$f = fopen('php://memory', 'w');

$fields = array('ID', 'State', 'City', 'Taluka', 'Area', 'Industrial Estate');
$delimiter = ",";
fputcsv($f, $fields, $delimiter);

while ($row0 = mysqli_fetch_array($result)) {
    $lineData = array($row0['id'], $row0['state_id'], $row0['city_id'], $row0['taluka'], $row0['area_id'], $row0['industrial_estate']);
    fputcsv($f, $lineData, $delimiter);
}

fseek($f, 0);

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/csv");

fpassthru($f);

?>
