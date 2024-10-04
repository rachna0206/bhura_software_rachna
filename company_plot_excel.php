<?php
include "db_connect.php";
$obj = new DB_connect();

// Retrieving parameters
$firm_name = isset($_REQUEST['firm_name']) ? $_REQUEST['firm_name'] : "";
$gst_no = isset($_REQUEST['gst_no']) ? $_REQUEST['gst_no'] : "";
$plot_no = isset($_REQUEST['plot_no']) ? $_REQUEST['plot_no'] : "";
$floor = isset($_REQUEST['floor']) ? $_REQUEST['floor'] : "";
$road_no = isset($_REQUEST['road_no']) ? $_REQUEST['road_no'] : "";
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : "";
$plot_status = isset($_REQUEST['plot_status']) ? $_REQUEST['plot_status'] : "";
$contact_person = isset($_REQUEST['contact_person']) ? $_REQUEST['contact_person'] : "";
$contact_number = isset($_REQUEST['contact_number']) ? $_REQUEST['contact_number'] : "";
$constitution = isset($_REQUEST['constitution']) ? $_REQUEST['constitution'] : "";
$remark = isset($_REQUEST['remark']) ? $_REQUEST['remark'] : "";
$segment = isset($_REQUEST['segment']) ? $_REQUEST['segment'] : "";
$ind_estate = isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "";

$firm_name_str = ($firm_name != "") ? " and lower(r1.raw_data->'$.post_fields.Firm_Name') like ?" : "";
$gst_no_str = ($gst_no != "") ? " and lower(r1.raw_data->'$.post_fields.Gst_No') like ?" : "";
$plot_no_str = ($plot_no != "") ? " and lower(r1.raw_data->'$.post_fields.Plot_No') like ?" : "";
$floor_str = ($floor != "") ? " and lower(r1.raw_data->'$.post_fields.Floor') like ?" : "";
$road_no_str = ($road_no != "") ? " and lower(r1.raw_data->'$.post_fields.Road_No') like ?" : "";
$status_str = ($status != "") ? " and c1.status=?" : "";
$plot_status_str = ($plot_status != "") ? " and p1.plot_status=?" : "";
$contact_person_str = ($contact_person != "") ? " and lower(r1.raw_data->'$.post_fields.Contact_Name') like ?" : "";
$contact_number_str = ($contact_number != "") ? " and lower(r1.raw_data->'$.post_fields.Mobile_No') like ?" : "";
$constitution_str = ($constitution != "") ? " and lower(c1.constitution) like ?" : "";
$remark_str = ($remark != "") ? " and lower(r1.raw_data->'$.post_fields.Remark') like ?" : "";
$segment_str = ($segment != "") ? " and lower(r1.raw_data->'$.post_fields.Segment') like ?" : "";
$ind_estate_str = ($ind_estate != "") ? " AND LOWER(i1.industrial_estate) = '" . strtolower($ind_estate) . "'" : "";
$cookie_str = (isset($_COOKIE['report_estate_id']) && !empty($_COOKIE['report_estate_id'])) ? " AND i1.id = '" . $_COOKIE['report_estate_id'] . "'" : "";
// Build final query
$query = "SELECT p1.pid as id, 
                 r1.raw_data->>'$.post_fields.Firm_Name' as firm_name, 
                 r1.raw_data->>'$.post_fields.GST_No' as gst_no, 
                 p1.plot_no, p1.floor, p1.road_no, p1.plot_status, 
                 r1.raw_data->>'$.post_fields.Contact_Name' as contact_person, 
                 r1.raw_data->>'$.post_fields.Mobile_No' as contact_number, 
                 c1.status, c1.constitution, r1.raw_data->>'$.post_fields.Remarks' as remark, 
                 r1.raw_data->>'$.post_fields.Segment' as segment 
          FROM pr_company_plots p1 
          JOIN tbl_industrial_estate i1 ON p1.industrial_estate_id = i1.id 
          LEFT JOIN pr_company_details c1 ON p1.company_id = c1.cid 
          LEFT JOIN tbl_tdrawdata r1 ON c1.rawdata_id = r1.id 
          WHERE 1=1 
          " . $firm_name_str . $gst_no_str . $plot_no_str . $floor_str . $road_no_str . $status_str . $plot_status_str . $ind_estate_str . $contact_person_str . $contact_number_str . $constitution_str . $remark_str . $segment_str . $cookie_str . "
          ORDER BY p1.industrial_estate_id, abs(p1.road_no), abs(p1.plot_no), p1.floor";

$stmt_list = $obj->con1->prepare($query);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();


$filename = "Company Plot.csv";

$f = fopen('php://memory', 'w');

$fields = array('ID', 'Firm Name ', 'GST No.', 'Plot No.', 'Floor', 'Road No.', 'Status', 'Plot Status', 'Contact Person', 'Contact Number', 'Constitution', 'Ramark', 'Segment');
$delimiter = ",";
fputcsv($f, $fields, $delimiter);

$i = 1;
while ($row0 = mysqli_fetch_array($result)) {


    $lineData = array($row0['id'], $row0['firm_name'], $row0['gst_no'], $row0['plot_no'], $row0['floor'], $row0['road_no'], $row0['status'], $row0['plot_status'], $row0['contact_person'], $row0['contact_number'], $row0['constitution'], $row0['remark'], $row0['segment']);
    fputcsv($f, $lineData, $delimiter);


    $i++;

}

fseek($f, 0);

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/csv");

fpassthru($f);

?>