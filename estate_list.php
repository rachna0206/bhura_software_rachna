<?php
include("header.php");
//error_reporting(0);

$plot_status="";

$stmt_area_list = $obj->con1->prepare("select distinct(area_id) from tbl_industrial_estate");
$stmt_area_list->execute();
$area_result = $stmt_area_list->get_result();
$stmt_area_list->close();

$stmt_ind_estate_list = $obj->con1->prepare("select distinct(industrial_estate) from tbl_industrial_estate");
$stmt_ind_estate_list->execute();
$ind_estate_result = $stmt_ind_estate_list->get_result();
$stmt_ind_estate_list->close();

$stmt_list = $obj->con1->prepare("SELECT i1.* FROM tbl_industrial_estate i1, (SELECT DISTINCT(industrial_estate_id) as estate_id FROM pr_company_plots p1) tbl1 WHERE i1.id=tbl1.estate_id ORDER BY state_id, city_id, taluka, area_id, industrial_estate;");
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();

?>

  <!-- Basic Bootstrap Table -->
    <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
    <div class="col-md-9"><h5 class="card-header">Industrial Estate List</h5></div>
      <input type="button" class="btn btn-primary" name="btn_excel" value="Download Excel" 
               onClick="javascript:estateGrid('<?php echo isset($_REQUEST['name']) ? $_REQUEST['name'] : "" ?>',
                                               '<?php echo isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : "" ?>',
                                               '<?php echo isset($_COOKIE['taluka']) ? $_COOKIE['taluka'] : "" ?>',
                                               '<?php echo isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : "" ?>',
                                               '<?php echo isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "" ?>',)" 
               id="btn_excel">
    </div>
      <div class="table-responsive text-nowrap">
        <table class="table" id="table_id">
          <thead>
            <tr>
              <th>Srno</th>
              <th>State</th>
              <th>City</th>
              <th>Taluka</th>
              <th>Area</th>
              <th>Industrial Estate</th>
              <th>View Report</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0" id="grid">
            <?php 
            $i=1;
              while($data=mysqli_fetch_array($result))
              {
            
            ?>

            <tr>
              <td><?php echo $i ?></td>
              <td><?php echo $data['state_id'] ?></td>
              <td><?php echo $data['city_id'] ?></td>
              <td><?php echo $data['taluka'] ?></td>
              <td><?php echo $data['area_id'] ?></td>
              <td><?php echo $data['industrial_estate'] ?></td>
              <td><a href="javascript:viewdata('<?php echo $data["id"]?>');"><i class="bx bx-show me-1"></i> </a></td>
            </tr>  
          <?php 
                $i++;
          } ?>
            
            
          </tbody>
        </table>
      </div>
    </div>

    <!--/ Basic Bootstrap Table -->

<script type="text/javascript">

  function viewdata(estate_id){
    // window.open('company_plot_report.php?estate_id='+estate_id, '_blank');
    window.open('company_plot_report.php', '_blank');
    createCookie("report_estate_id",estate_id,1);
  }
  function estateGrid(name,city_id,taluka,area_id,industrial_estate){
    const arr = [name,city_id,taluka,area_id,industrial_estate];
    window.open('estate_list_excel.php', '_blank');
    document.cookie = "report_search="+arr;
  }

</script>

<?php 
  include("footer.php");
?>