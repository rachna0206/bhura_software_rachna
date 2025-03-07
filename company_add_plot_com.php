<?php
include("header.php");

include("company_add_plot_excel.php");
?>

<h4 class="fw-bold py-3 mb-4">Add Plotting in Company (No Industrial Estate)</h4>

<?php
if (isset($_REQUEST['download_single'])) {
  try {
   // $stmt = $obj->con1->prepare("SELECT r1.id, r1.raw_data->>'$.post_fields.Firm_Name' as firm_name, r1.raw_data->>'$.post_fields.Factory_Address' as factory_address, r1.raw_data->>'$.post_fields.Mobile_No' as mobile_no,r1.raw_data->>'$.post_fields.Contact_Name' as contact_name, (select stage END from tbl_tdrawassign where inq_id=r1.id order by id desc LIMIT 1) stage, (select CASE WHEN stage='lead' THEN 'Positive' WHEN stage='badlead' THEN 'Negative' ELSE 'Existing Client' END from tbl_tdrawassign where inq_id=r1.id order by id desc LIMIT 1) stage1 from tbl_tdrawdata r1 where r1.raw_data->'$.post_fields.city'='" . $_REQUEST["city"] . "' and r1.raw_data->'$.post_fields.Taluka'='" . $_REQUEST["taluka"] . "' and r1.raw_data->'$.post_fields.Area'='" . $_REQUEST["area"] . "' and JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 and raw_data->'$.post_fields.IndustrialEstate'='' and id not in (SELECT rawdata_id from pr_company_details)");

   $stmt=$obj->con1->prepare("SELECT 
    r1.id, 
    r1.raw_data->>'$.post_fields.Firm_Name' AS firm_name, 
    r1.raw_data->>'$.post_fields.Factory_Address' AS factory_address, 
    r1.raw_data->>'$.post_fields.Mobile_No' AS mobile_no,
    r1.raw_data->>'$.post_fields.Contact_Name' AS contact_name, 
    r1.raw_data->>'$.post_fields.Taluka'  as taluka,
    r1.raw_data->>'$.post_fields.Area' as area,
    r1.raw_data->>'$.post_fields.city' as city,
    ta.stage, 
    CASE 
        WHEN ta.stage = 'lead' THEN 'Positive' 
        WHEN ta.stage = 'badlead' THEN 'Negative' 
        ELSE 'Existing Client' 
    END AS stage1,
    u1.name AS emp_name
FROM tbl_tdrawdata r1
LEFT JOIN (
    
    SELECT inq_id, stage, user_id
    FROM tbl_tdrawassign
    WHERE (inq_id, id) IN (
        SELECT inq_id, MAX(id)
        FROM tbl_tdrawassign
        GROUP BY inq_id
    )
) ta ON ta.inq_id = r1.id
LEFT JOIN tbl_users u1 ON ta.user_id = u1.id
WHERE 
    ta.stage NOT IN ('applicationstart', 'schemesstarted')
    AND r1.raw_data->'$.post_fields.Taluka' = '" . $_REQUEST["taluka"] . "'
    AND r1.raw_data->'$.post_fields.Area' ='" . $_REQUEST["area"] . "'
    AND r1.raw_data->'$.post_fields.city' = '" . $_REQUEST["city"] . "'
    AND JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 
    AND raw_data->'$.post_fields.IndustrialEstate' = '' 
    AND r1.id NOT IN (SELECT rawdata_id FROM pr_company_details)

UNION 

SELECT 
    r1.id, 
    r1.raw_data->>'$.post_fields.Firm_Name' AS firm_name, 
    r1.raw_data->>'$.post_fields.Factory_Address' AS factory_address, 
    r1.raw_data->>'$.post_fields.Mobile_No' AS mobile_no,
    r1.raw_data->>'$.post_fields.Contact_Name' AS contact_name, 
   
    r1.raw_data->>'$.post_fields.Taluka'  as taluka,
    r1.raw_data->>'$.post_fields.Area' as area,
    r1.raw_data->>'$.post_fields.city' as city,
     ta2.tatassign_status AS stage, 
    NULL AS stage1, 
    u1.name AS emp_name
FROM tbl_tdrawdata r1
LEFT JOIN (
   
    SELECT a1.tatassign_inq_id AS inq_id, 
           a1.tatassign_status, 
           a1.tatassign_user_id
    FROM tbl_tdtatassign a1
    WHERE (a1.tatassign_inq_id, a1.tatassign_id) IN (
        SELECT tatassign_inq_id, MAX(tatassign_id)
        FROM tbl_tdtatassign
        GROUP BY tatassign_inq_id
    )
) ta2 ON ta2.inq_id = r1.id
LEFT JOIN tbl_users u1 ON ta2.tatassign_user_id = u1.id
WHERE 
    EXISTS (
        SELECT 1 FROM tbl_tdrawassign ta 
        WHERE ta.inq_id = r1.id 
        AND ta.stage IN ('applicationstart', 'schemesstarted')
    )
    AND r1.raw_data->'$.post_fields.Taluka' = '" . $_REQUEST["taluka"] . "'
    AND r1.raw_data->'$.post_fields.Area' = '" . $_REQUEST["area"] . "'
    AND r1.raw_data->'$.post_fields.city' = '" . $_REQUEST["city"] . "'
    AND JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 
    AND raw_data->'$.post_fields.IndustrialEstate' = '' 
    AND r1.id NOT IN (SELECT rawdata_id FROM pr_company_details)");


    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    $file_name = "excel_" . uniqid() . ".xlsx";
    dynamic_excel_generate($res, $file_name);

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file_name));
    header('Content-Length: ' . filesize($file_name));
    ob_clean();
    flush();
    readfile($file_name);
    unlink($file_name);

    // exit;
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}



if (isset($_COOKIE["msg"])) {

  if ($_COOKIE['msg'] == "data") {

    ?>
    <div class="alert alert-primary alert-dismissible" role="alert">
      Data added succesfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
  }
  if ($_COOKIE['msg'] == "update") {

    ?>
    <div class="alert alert-primary alert-dismissible" role="alert">
      Data updated succesfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
  }
  if ($_COOKIE['msg'] == "data_del") {

    ?>
    <div class="alert alert-primary alert-dismissible" role="alert">
      Data deleted succesfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
  }
  if ($_COOKIE['msg'] == "fail") {
    ?>

    <div class="alert alert-danger alert-dismissible" role="alert">
      An error occured! Try again.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
  }
}
if (isset($_COOKIE["sql_error"])) {
  ?>
  <div class="alert alert-danger alert-dismissible" role="alert">
    <?php echo urldecode($_COOKIE['sql_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>

  <script type="text/javascript">eraseCookie("sql_error")</script>
  <?php
}


function download_excel($obj)
{
  try {
    // Prepare the main query
    $stmt_list = $obj->con1->prepare("SELECT i1.* from (SELECT DISTINCT json_unquote(raw_data->'$.post_fields.Taluka') as taluka, json_unquote(raw_data->'$.post_fields.Area') as area, json_unquote(raw_data->'$.post_fields.IndustrialEstate') as ind_estate FROM tbl_tdrawdata WHERE JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 and raw_data->'$.post_fields.IndustrialEstate'!='') tbl1, tbl_industrial_estate i1 where tbl1.taluka=i1.taluka and tbl1.area=i1.area_id and tbl1.ind_estate=i1.industrial_estate and id not in (SELECT rawdata_id from pr_company_details) order by i1.taluka,i1.area_id,i1.industrial_estate");
    $stmt_list->execute();
    $excel_data = $stmt_list->get_result();
    $stmt_list->close();

    $files_list = []; // To store generated file paths

    while ($data = $excel_data->fetch_assoc()) {

      $stmt = $obj->con1->prepare("SELECT DISTINCT json_unquote(raw_data->'$.post_fields.state') as state, json_unquote(raw_data->'$.post_fields.city') as city, json_unquote(raw_data->'$.post_fields.Taluka') as taluka, json_unquote(raw_data->'$.post_fields.Area') as area FROM tbl_tdrawdata WHERE JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 and raw_data->'$.post_fields.IndustrialEstate'='' and id not in (SELECT rawdata_id from pr_company_details) order by state,city,taluka,area");

      $stmt->execute();
      $res = $stmt->get_result();
      $stmt->close();

      $file_name = "excel_" . uniqid() . ".xlsx";
      dynamic_excel_generate($res, $file_name);
      $files_list[] = $file_name;
    }

    // Create and download ZIP file
    createAndDownloadZip($files_list, 'data_files.zip');
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
}

if (isset($_REQUEST["btn_excel"])) {
  echo "excel download started";
  download_excel($obj);
}
?>

<!-- grid -->

<!-- Modal -->
<div class="modal fade" id="modalCenter" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Fill Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          <div id="modal_form_div">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- /modal-->


<!-- Basic Bootstrap Table -->
<?php
/* if(!in_array($user_id, $admin)){ ?>
<div class="card">
  <h5 class="card-header">Records (Company)</h5>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover" id="table_id">
      <thead>
        <tr>
          <th>Srno</th>
          <th>Taluka</th>
          <th>Area</th>
          <th>Company Name</th>
          <th>Employee Name</th>
          <th>Factory Address</th>
          <th>Status</th>
          <th>Action</th>  
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        <?php
        // SELECT DISTINCT json_unquote(raw_data->'$.post_fields.Taluka') as taluka, json_unquote(raw_data->'$.post_fields.Area') as area FROM tbl_tdrawdata WHERE JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 and raw_data->'$.post_fields.IndustrialEstate'='' and id not in (SELECT rawdata_id from pr_company_details) order by taluka,area
          $stmt_list = $obj->con1->prepare("SELECT id, raw_data->>'$.post_fields.state' as state, raw_data->>'$.post_fields.city' as city, raw_data->>'$.post_fields.Taluka' as taluka, raw_data->>'$.post_fields.Area' as area, raw_data->>'$.post_fields.IndustrialEstate' as industrial_estate, raw_data->>'$.post_fields.Firm_Name' as firm_name, raw_data->>'$.post_fields.Factory_Address' as factory_address FROM tbl_tdrawdata WHERE JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 and raw_data->'$.post_fields.IndustrialEstate'='' and id not in (SELECT rawdata_id from pr_company_details)");
          $stmt_list->execute();
          $result = $stmt_list->get_result();
          $stmt_list->close();
          $i=1;

          while($data=mysqli_fetch_array($result))
          {
            $stmt_status = $obj->con1->prepare("SELECT stage, CASE WHEN stage='lead' THEN 'Positive' WHEN stage='badlead' THEN 'Negative' ELSE 'Existing Client' END stage1 FROM `tbl_tdrawassign` WHERE inq_id=? order by id desc LIMIT 1");
            $stmt_status->bind_param("i",$data['id']);
            $stmt_status->execute();
            $result_status = $stmt_status->get_result()->fetch_assoc();
            $stmt_status->close();

            if($result_status["stage"]=='applicationstart' || $result_status["stage"]=='schemesstarted')
            {
              $stmt_stage = $obj->con1->prepare("select a1.tatassign_id, a1.tatassign_status ,a1.tatassign_user_id,u1.name as emp_name from tbl_tdtatassign a1,tbl_users u1 where a1.tatassign_user_id=u1.id and  a1.tatassign_inq_id=?  order by a1.tatassign_id desc limit 1");
              $stmt_stage->bind_param("i",$data["id"]);
              $stmt_stage->execute();
              $stage_result = $stmt_stage->get_result()->fetch_assoc();
              $stmt_stage->close();
              $emp_name=$stage_result["emp_name"];
            }
            else
            {
              $stmt_stage = $obj->con1->prepare("select r1.id, r1.stage ,r1.user_id,u1.name as emp_name from tbl_tdrawassign r1,tbl_users u1 where r1.user_id=u1.id and r1.inq_id=? order by r1.id desc limit 1; ");
              $stmt_stage->bind_param("i",$data["id"]);
              $stmt_stage->execute();
              $stage_result = $stmt_stage->get_result()->fetch_assoc();
              $stmt_stage->close();
              $emp_name=$stage_result["emp_name"];

            }

           if($emp_name==$_SESSION["username"])
          {

            
        ?>

        <tr>
          <td><?php echo $i?></td>
          <td><?php echo $data["taluka"] ?></td>
          <td><?php echo $data["area"] ?></td>
          <td><?php echo $data["firm_name"] ?></td>
          <td><?php echo $emp_name ?></td>
          <td><?php echo $data["factory_address"] ?></td>
          <td><?php echo $result_status["stage1"] ?></td>
          <td>
            <a href="javascript:editdata('<?php echo $data["id"]?>','<?php echo base64_encode($data["state"]) ?>','<?php echo base64_encode($data["city"]) ?>','<?php echo base64_encode($data["taluka"]) ?>','<?php echo base64_encode($data["area"]) ?>','<?php echo base64_encode($data["firm_name"]) ?>','<?php echo $user_id ?>','<?php echo base64_encode($result_status["stage1"]) ?>','<?php echo base64_encode($data["factory_address"]) ?>','<?php echo base64_encode($emp_name) ?>');"><i class="bx bx-edit-alt me-1"></i> </a>
          </td>
        </tr>
        <?php
        $i++;
            }
            
          }
        ?>
        
      </tbody>
    </table>
  </div>
</div>

<?php } 
*/ ?>

<?php //if(in_array($user_id, $admin)){ ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">

    <h5 class="card-header">Records (Company)</h5>
    <form action="" method="post">
      <input type="submit" class="btn btn-primary" name="btn_excel" value="Download Excel" id="btn_excel">
    </form>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover" id="table_id">
      <thead>
        <tr>
          <th>Srno</th>
          <th>State</th>
          <th>City</th>
          <th>Taluka</th>
          <th>Area</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        <?php
        $stmt_list = $obj->con1->prepare("SELECT DISTINCT json_unquote(raw_data->'$.post_fields.state') as state, json_unquote(raw_data->'$.post_fields.city') as city, json_unquote(raw_data->'$.post_fields.Taluka') as taluka, json_unquote(raw_data->'$.post_fields.Area') as area FROM tbl_tdrawdata WHERE JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0 and raw_data->'$.post_fields.IndustrialEstate'='' and id not in (SELECT rawdata_id from pr_company_details) order by state,city,taluka,area");
        $stmt_list->execute();
        $result = $stmt_list->get_result();
        $stmt_list->close();
        $i = 1;

        while ($data = mysqli_fetch_array($result)) {

          ?>

          <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $data["state"] ?></td>
            <td><?php echo $data["city"] ?></td>
            <td><?php echo $data["taluka"] ?></td>
            <td><?php echo $data["area"] ?></td>
            <td>
              <a
                href="javascript:viewdata('<?php echo base64_encode($data["state"]) ?>','<?php echo base64_encode($data["city"]) ?>','<?php echo base64_encode($data["taluka"]) ?>','<?php echo base64_encode($data["area"]) ?>','<?php echo $user_id ?>');">View</a>
              <a
                href="company_add_plot_com.php?download_single=true&city=<?php echo $data['city'] ?>&taluka=<?php echo $data['taluka'] ?>&area=<?php echo $data['area'] ?>"><i
                  class="bx bx-download"> </i></a>

            </td>
          </tr>
          <?php
          $i++;
        }
        ?>

      </tbody>
    </table>
  </div>
</div>

<?php // } ?>
<!--/ Basic Bootstrap Table -->

<!-- / grid -->

<!-- / Content -->
<script type="text/javascript">

  /*window.addEventListener('focus', function() {
    console.log('show');

  }, false);
  window.addEventListener('blur', function() {
     console.log('hide');
  }, false);*/

  function viewdata(state, city, taluka, area, user_id) {
    $('#modalCenter').modal('toggle');

    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=company_plot_comp_grid",
      data: "state=" + atob(state) + "&city=" + atob(city) + "&taluka=" + atob(taluka) + "&area=" + atob(area) + "&user_id=" + user_id,
      cache: false,
      beforeSend: function () {
        // Show loader before the AJAX call
        //$('#loader').show();
        $('#modal_form_div').html('');
        $('#modal_form_div').html(`
            <div class="d-flex align-items-center justify-content-center">
              <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        `);
      },
      success: function (result) {
        $('#modal_form_div').html('');
        $('#modal_form_div').html(result);
        $('#table_modal_id').DataTable();
      }
    });

  }

  function editdata(rawdata_id, state, city, taluka, area, firm_name, user_id, status, factory_address, emp_name) {

    createCookie('state_comp_addplot', atob(state));
    createCookie('city_comp_addplot', atob(city));
    createCookie('taluka_comp_addplot', atob(taluka));
    createCookie('area_comp_addplot', atob(area));
    createCookie('rawdataid_comp_addplot', rawdata_id);
    createCookie('company_status', atob(status));
    createCookie('empname_comp_addplot', atob(emp_name));
    createCookie('selecttype_comp_addplot', 'select_company_first');

    localStorage.setItem("factoryadd_comp_addplot", atob(factory_address));

    // createCookie('redirection_pagename', 'company_add_plot_com.php');
    window.open("company_add_plot.php", '_blank');
  }
</script>
<?php
include("footer.php");
?>