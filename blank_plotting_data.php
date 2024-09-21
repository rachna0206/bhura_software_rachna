<?php

include("db_connect.php");
$obj=new DB_Connect();

//include "header.php";



?>	

  <form method="post" >
    Limit: <input type="text" name="limit">
    Offset: <input type="text" name="offset">
    <input type="submit" name="submit" value="Submit">
  </form>

<!-- Basic Bootstrap Table -->
    <div class="card">
      <h5 class="card-header">Blank Companies</h5>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover" id="table_id" style="border-collapse: collapse; border: 1px solid;">
          <thead>
            <tr>
              <th style="border: 1px solid;">Srno</th>
              <th style="border: 1px solid;">Taluka</th>
              <th style="border: 1px solid;">Area</th>  
              <th style="border: 1px solid;">Industrial Estate</th>
              <th style="border: 1px solid;">Count</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php

            if(isset($_REQUEST['submit'])){
              $limit = $_REQUEST['limit'];
              $offset = $_REQUEST['offset'];

              $stmt = $obj->con1->prepare("SELECT d1.status,i1.id,i1.taluka,i1.area_id,i1.industrial_estate,d1.plotting_pattern FROM `pr_add_industrialestate_details` d1, tbl_industrial_estate i1 where d1.industrial_estate_id=i1.id and status='Verified' limit ? offset ?");
              $stmt->bind_param("ii",$limit,$offset);
              $stmt->execute();
              $res = $stmt->get_result();
              $stmt->close();

            $i=1;
              while ($ind_res = mysqli_fetch_array($res)) {
                
	
  				$stmt_comp = $obj->con1->prepare("SELECT json_unquote(raw_data->'$.post_fields.Taluka') as taluka, json_unquote(raw_data->'$.post_fields.Area') as area, json_unquote(raw_data->'$.post_fields.IndustrialEstate') as industrial_estate, count(*) as counter from tbl_tdrawdata where lower(raw_data->'$.post_fields.Taluka') like '%".strtolower($ind_res['taluka'])."%' and lower(raw_data->'$.post_fields.Area') like '%".strtolower($ind_res['area_id'])."%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%".strtolower($ind_res['industrial_estate'])."%' and  JSON_CONTAINS_PATH(raw_data, 'one', '$.plot_details') = 0");
  				$stmt_comp->execute();
  				$res_comp = $stmt_comp->get_result();
  				$stmt_comp->close();

				while($comp = mysqli_fetch_array($res_comp)){
          
            ?>

            <tr>
              <td style="border: 1px solid;"><?php echo $i?></td>
              <td style="border: 1px solid;"><?php echo $ind_res["taluka"] ?></td>
              <td style="border: 1px solid;"><?php echo $ind_res["area_id"] ?></td>
              <td style="border: 1px solid;"><?php echo $ind_res["industrial_estate"] ?></td>
              <td style="border: 1px solid;"><?php echo $comp["counter"] ?></td>
            </tr>
            <?php
                $i++;
					}
				}
      }
            ?>
            
          </tbody>
        </table>
      </div>
    </div>
    <!--/ Basic Bootstrap Table -->

  <!-- / grid -->

<?php
	


//include "footer.php";

?>