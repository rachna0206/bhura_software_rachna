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
    echo 1;
    $query .= " AND DATE_FORMAT(p1.date_time, '%Y-%m-%d') >= ?";
    $param_str .= "s";
    array_push($params, $_REQUEST["start_date"]);
}

if ($_REQUEST["end_date"] != "") {
    echo 2;
    $query .= " AND DATE_FORMAT(p1.date_time, '%Y-%m-%d') <= ?";
    $param_str .= "s";
    array_push($params, $_REQUEST["end_date"]);
}

if ($_REQUEST["select_user_id"] != "") {
    echo 3;
    $query .= " AND p1.user_id = ?";
    $param_str .= "s";
    array_push($params, $_REQUEST["select_user_id"]);
}

$stmt_list = $obj->con1->prepare($query);
$stmt_list->bind_param($param_str, ...$params);

// $stmt_list->bind_param("sss", $_REQUEST["start_date"], $_REQUEST["end_date"], $_REQUEST["select_user_id"]);
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();
$i = 1;

while ($data = mysqli_fetch_array($result)) {
    ?>

    <tr>
        <td><?php echo $i ?></td>
        <td><?php echo $data["industrial_estate"] ?></td>
        <td><?php echo $data["company"] ?></td>
        <td><?php echo $data["name"] ?></td>
        <td><?php echo $data["operation"] ?></td>
        <td><?php echo date("d-m-Y h:i A", strtotime($data["date_time"])) ?></td>
    </tr>
    <?php
    $i++;
}
?>