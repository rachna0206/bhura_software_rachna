<?php
include "db_connect.php";
$obj = new DB_connect();

$state = isset($_REQUEST['state_id']) ? $_REQUEST['state_id'] : "";
$city = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : "";
$taluka = isset($_REQUEST['taluka']) ? $_REQUEST['taluka'] : "";
$area = isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : "";
$ind_estate = isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "";
$plotting_pattern = isset($_REQUEST['plotting_pattern']) ? $_REQUEST['plotting_pattern'] : "";
$total_plots = isset($_REQUEST['total']) ? $_REQUEST['total'] : "";
$added_by = isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : "";
$date_time = isset($_REQUEST['datetime']) ? $_REQUEST['datetime'] : "";

// Construct the WHERE clause based on the filters
$state_str = ($state != "") ? " AND lower(i1.state_id) = '" . strtolower($state) . "'" : "";
$city_str = ($city != "") ? " AND lower(i1.city_id) = '" . strtolower($city) . "'" : "";
$taluka_str = ($taluka != "") ? " AND lower(i1.taluka) = '" . strtolower($taluka) . "'" : "";
$area_str = ($area != "") ? " AND lower(i1.area_id) = '" . strtolower($area) . "'" : "";
$ind_estate_str = ($ind_estate != "") ? " AND lower(i1.industrial_estate) = '" . strtolower($ind_estate) . "'" : "";
$cookie_str = isset($_COOKIE['report_estate_id']) && !empty($_COOKIE['report_estate_id']) ? " AND i1.id = '" . $_COOKIE['report_estate_id'] . "'" : "";
$plotting_pattern_str = ($plotting_pattern != "") ? " AND lower(d1.plotting_pattern) = '" . strtolower($plotting_pattern) . "'" : "";
$total_plots_str = ($total_plots != "") ? " AND count(p1.plot_no) = '" . strtolower($total_plots) . "'" : "";
$added_by_str = ($added_by != "") ? " AND lower(u1.name) = '" . strtolower($added_by) . "'" : "";
$date_time_str = ($date_time != "") ? " AND lower(d1.datetime) = '" . strtolower($date_time) . "'" : "";

// Complete query with filters
$query = "SELECT i1.state_id, i1.city_id, i1.taluka, i1.area_id, i1.industrial_estate, 
                 d1.plotting_pattern, d1.status, i1.id as industrial_estate_id, count(p1.plot_no) as total,
                 u1.name as user_name, d1.datetime 
          FROM pr_add_industrialestate_details d1 
          INNER JOIN tbl_industrial_estate i1 ON d1.industrial_estate_id = i1.id 
          INNER JOIN pr_company_plots p1 ON i1.id = p1.industrial_estate_id 
          INNER JOIN tbl_users u1 ON d1.user_id = u1.id 
          WHERE (d1.status IS NULL OR d1.status NOT IN ('Fake', 'Duplicate')) 
          AND d1.industrial_estate_id NOT IN 
              (SELECT industrial_estate_id FROM assign_estate WHERE action='company_entry')
          $state_str $city_str $taluka_str $area_str $ind_estate_str $cookie_str $plotting_pattern_str $total_plots_str $added_by_str $date_time_str 
          GROUP BY industrial_estate_id 
          ORDER BY d1.id DESC";

$stmt_list = $obj->con1->prepare($query);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();

// file name for download
$filename = "Unassigned Estate Company.csv";
// Create a file pointer 
$f = fopen('php://memory', 'w');
// Set column headers 
$fields = array('ID', 'State', 'City', 'Taluka', 'Area', 'Industrial Estate', 'Plotting Pattern', 'Total Plots', 'Added By', 'Date Time');
$delimiter = ",";
fputcsv($f, $fields, $delimiter);

while ($row0 = mysqli_fetch_array($result)) {
    $lineData = array(
        $row0['industrial_estate_id'],
        $row0['state_id'],
        $row0['city_id'],
        $row0['taluka'],
        $row0['area_id'],
        $row0['industrial_estate'],
        $row0['plotting_pattern'],
        $row0['total'],
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