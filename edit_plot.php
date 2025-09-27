<?php
  include("header.php");
  

    $plot_id=$_COOKIE["edit_plot"];
    //echo "select * from pr_company_plots p, tbl_industrial_estate e1 where  industrial_estate_id=e1.id and p.pid=$plot_id";
    $stmt_estate = $obj->con1->prepare("select * from pr_company_plots p, tbl_industrial_estate e1 where  industrial_estate_id=e1.id and p.pid=?");
    $stmt_estate->bind_param("i", $plot_id);
    $stmt_estate->execute();
    $plot_data = $stmt_estate->get_result()->fetch_assoc();
    $stmt_estate->close();

   // echo "<br>SELECT * FROM tbl_tdrawdata WHERE lower(raw_data->'$.post_fields.Taluka') like '%" . strtolower($plot_data["taluka"]) . "%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%" . strtolower($plot_data["industrial_estate"]) . "%' and lower(raw_data->'$.post_fields.Area') like '%" . strtolower($plot_data["area_id"]) . "%' AND JSON_CONTAINS(raw_data->'$.plot_details', JSON_OBJECT('Plot_No', ".$plot_data["plot_no"]."))AND JSON_CONTAINS(raw_data->'$.plot_details', JSON_OBJECT('Road_No','".$plot_data["road_no"]."'))";

    //fetch data from tdrawdata
    $stmt_plot =  $obj->con1->prepare("SELECT * FROM tbl_tdrawdata WHERE lower(raw_data->'$.post_fields.Taluka') like '%" . strtolower($plot_data["taluka"]) . "%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%" . strtolower($plot_data["industrial_estate"]) . "%' and lower(raw_data->'$.post_fields.Area') like '%" . strtolower($plot_data["area_id"]) . "%' AND JSON_CONTAINS(raw_data->'$.plot_details', JSON_OBJECT('Plot_No', ".$plot_data["plot_no"]."))
  AND JSON_CONTAINS(raw_data->'$.plot_details', JSON_OBJECT('Road_No','".$plot_data["road_no"]."'))");
    $stmt_plot->execute();
    $plot_res = $stmt_plot->get_result();
    $stmt_plot->close();

    $estate = mysqli_fetch_array($plot_res);
      // Decode JSON into a PHP associative array
      $data = json_decode($estate["raw_data"], true);
      $post_fields=$data["post_fields"];
      
      $plot_status=$data["plot_details"][0]["Plot_Status"];
      

 
// insert data for select estate first
if(isset($_REQUEST['btn_update_plot']))
{
  
  $plot_id=$_COOKIE["edit_plot"];
  $firm_name = $_REQUEST['firm_name'];
  
  $gst_no = $_REQUEST['gst_no'];
  $plot_status_new = $_REQUEST['plot_status'];
  $contact_person = $_REQUEST['contact_person'];
  $contact = $_REQUEST['contact'];
  $status=$_REQUEST['status'];
  $constitution=$_REQUEST['constitution'];
  $remark=$_REQUEST['remark'];
  $segment=$_REQUEST['segment'];


  $rawdata = json_decode($estate["raw_data"]);
  $plot_details=$rawdata->plot_details[0];
 
  //print_r($rawdata);

  $rawdata->Status=$status;
  $rawdata->post_fields->Segment=$segment;
  $rawdata->post_fields->Firm_Name=$firm_name;
  $rawdata->post_fields->Contact_Name=$contact_person;
  $rawdata->post_fields->Mobile_No=$contact;
  $rawdata->post_fields->GST_No=$gst_no;
  $rawdata->post_fields->Remarks=$remark;
  $rawdata->plot_details[0]->Plot_Status=$plot_status_new;

  
  $updatedJsonData=json_encode($rawdata);

  
  try {

   

    if($plot_data["company_id"]!="")
    {
      $comp_id=$plot_data["company_id"];
      //update pr_company_details
      $stmt_update_comp= $obj->con1->prepare("update  pr_company_details SET contact_name=?,mobile_no=?,gst_no=?,segment=?,remarks=?,constitution=?,`status`=? where cid=?");
      $stmt_update_comp->bind_param("sssssssi",$contact_person,$contact,$gst_no,$segment,$remark,$constitution,$status,$plot_data["company_id"]);
      $resp_comp=$stmt_update_comp->execute();
      $stmt_update_comp->close();

    }
    else
    {
      //insert into pr_company_details
          $stmt_pr_company_detail = $obj->con1->prepare("INSERT INTO `pr_company_details`(`source`, `source_name`, `contact_name`, `mobile_no`, `firm_name`, `gst_no`, `category`, `segment`, `premise`, `state`, `city`, `taluka`, `area`, `industrial_estate`, `inq_submit`, `industrial_estate_id`, `user_id`, `rawdata_id`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
      $stmt_pr_company_detail->bind_param("sssssssssssssssiiis",$source,$source_name,$contact_person,$contact_no,$firm_name,$gst_no,$category,$segment,$premise,$state,$city,$taluka,$area,$industrial_estate,$inq_submit,$industrial_estate_id,$user_id,$estate['id'],$status_company);
      $Resp=$stmt_pr_company_detail->execute();
      $last_insert_company_id = mysqli_insert_id($obj->con1);
      $stmt_pr_company_detail->close();
      $comp_id=$last_insert_company_id;


    }

    //update  pr_company_plot tbl
   
   $stmt_update_plot= $obj->con1->prepare("update  pr_company_plots SET plot_status=?,`company_id`=? where pid=?");
   $stmt_update_plot->bind_param("sii",$plot_status_new,$comp_id,$plot_id);
   $resp_plot=$stmt_update_plot->execute();
   $stmt_update_plot->close();
   


   //update tbl_tdrawdata
  
   $stmt_update = $obj->con1->prepare("update tbl_tdrawdata set raw_data=?, userid=? where id=?");
   $stmt_update->bind_param("sii",$updatedJsonData,$estate["userid"],$estate["id"]);
   $resp_update=$stmt_update->execute();
   $num_rows_aff = mysqli_affected_rows($obj->con1);
  $stmt_update->close();
  if (!$resp_update) {
    throw new Exception("Problem in saving! " . strtok($obj->con1->error,  '('));
  }
 
} catch (\Exception  $e) {
  setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
}

  





  // get industrial est name
  // $stmt_ind_est = $obj->con1->prepare("SELECT * FROM `tbl_industrial_estate` WHERE id=?");
  // $stmt_ind_est->bind_param("i",$industrial_estate_id);
  // $stmt_ind_est->execute();
  // $ind_est_result = $stmt_ind_est->get_result()->fetch_assoc();
  // $stmt_ind_est->close();

  // get id from tbl_tdrawdata of selected plot_no and floor_no
  // $stmt_plot_search = $obj->con1->prepare("SELECT * FROM tbl_tdrawdata WHERE raw_data->'$.plot_details[*].Plot_No' like '%".$plot_no."%' and raw_data->'$.plot_details[*].Road_No' like '%".$road_no."%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%".strtolower($ind_est_result['industrial_estate'])."%' and lower(raw_data->'$.post_fields.Area') like '%".strtolower($ind_est_result['area_id'])."%' and lower(raw_data->'$.post_fields.Taluka') like '%".strtolower($ind_est_result['taluka'])."%'");
  // $stmt_plot_search->execute();
  // $plot_search = $stmt_plot_search->get_result();
  // $stmt_plot_search->close();

  // $plot_rawdata_id = "";
  // if(mysqli_num_rows($plot_search)>0){
  //   while($plot_search_result=mysqli_fetch_array($plot_search)){
  //     $row_data_plot_search=json_decode($plot_search_result["raw_data"]);
  //     if($row_data_plot_search->post_fields->IndustrialEstate==$ind_est_result['industrial_estate'] && $row_data_plot_search->post_fields->Taluka==$ind_est_result['taluka'] && $row_data_plot_search->post_fields->Area==$ind_est_result['area_id']){
  //       foreach ($row_data_plot_search->plot_details as $pd) {
  //         if($pd->Plot_No==$plot_no && $pd->Floor==$floor_no && $pd->Road_No==$road_no){
  //           $plot_rawdata_id=$plot_search_result["id"];
  //           break;
  //         }
  //       }
  //     }
  //   }
  // }

  // if($plot_rawdata_id==""){
  //   // New Floor is Added

  //   $plot_id='1';
  //   $stmt_company_list = $obj->con1->prepare("SELECT * FROM `tbl_tdrawdata` WHERE id=?");
  //   $stmt_company_list->bind_param("i",$rawdata_id);
  //   $stmt_company_list->execute();
  //   $company_result = $stmt_company_list->get_result()->fetch_assoc();
  //   $stmt_company_list->close();
    
  //   // add array of plot in company json
  //   $row_data_comp=json_decode($company_result["raw_data"]);
   
  //   $row_data_comp->bad_lead_reason = "";
  //   $row_data_comp->bad_lead_reason_remark = "";
  //   $row_data_comp->Image = "";
  //   $row_data_comp->Constitution = "";
  //   $row_data_comp->Status = $status_company;
  //   $row_data_comp->post_fields->IndustrialEstate=$ind_est_result["industrial_estate"];
  //   $row_data_comp->post_fields->state=$ind_est_result["state_id"];
  //   $row_data_comp->post_fields->city=$ind_est_result["city_id"];
  //   $row_data_comp->post_fields->Taluka=$ind_est_result["taluka"];
  //   $row_data_comp->post_fields->Area=$ind_est_result["area_id"];
  //   $row_data_comp->post_fields->Factory_Address=$factory_address;
  //   $row_data_comp->plot_details = Array(
  //           Array(
  //             "Plot_No" => $plot_no,
  //             "Floor" => $floor_no,
  //             "Road_No" => $road_no,
  //             "Plot_Status" => "",
  //             "Plot_Id" => "1",
  //           ));
  //   $json_object = json_encode($row_data_comp);

  //   // get company details for pr_company_details
  //   $post_fields_comp = $row_data_comp->post_fields;
  //   $source = $post_fields_comp->source;
  //   $source_name = $post_fields_comp->Source_Name;
  //   $contact_person = $post_fields_comp->Contact_Name;
  //   $contact_no = $post_fields_comp->Mobile_No;
  //   $firm_name = $post_fields_comp->Firm_Name;
  //   $gst_no = $post_fields_comp->GST_No;
  //   $category = $post_fields_comp->Category;
  //   $segment = $post_fields_comp->Segment;
  //   $premise = $post_fields_comp->Premise;
  //   $state =$ind_est_result["state_id"];
  //   $city = $ind_est_result["city_id"];
  //   $taluka = $ind_est_result["taluka"];
  //   $area = $ind_est_result["area_id"];
  //   $industrial_estate = $ind_est_result["industrial_estate"];
  //   $inq_submit = "Submit";

  //   try {
  //     // update json of company
  //     $stmt = $obj->con1->prepare("UPDATE `tbl_tdrawdata` set raw_data=? where id=?");
  //     $stmt->bind_param("si",$json_object,$company_result['id']);
  //     $Resp=$stmt->execute();
  //     $stmt->close();

  //     // insert into pr_company_details and pr_company_plots
  //     $stmt_pr_company_detail = $obj->con1->prepare("INSERT INTO `pr_company_details`(`source`, `source_name`, `contact_name`, `mobile_no`, `firm_name`, `gst_no`, `category`, `segment`, `premise`, `state`, `city`, `taluka`, `area`, `industrial_estate`, `inq_submit`, `industrial_estate_id`, `user_id`, `rawdata_id`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
  //     $stmt_pr_company_detail->bind_param("sssssssssssssssiiis",$source,$source_name,$contact_person,$contact_no,$firm_name,$gst_no,$category,$segment,$premise,$state,$city,$taluka,$area,$industrial_estate,$inq_submit,$industrial_estate_id,$user_id,$company_result['id'],$status_company);
  //     $Resp=$stmt_pr_company_detail->execute();
  //     $last_insert_company_id = mysqli_insert_id($obj->con1);
  //     $stmt_pr_company_detail->close();

  //     // insert in pr_company_plot
  //     $stmt_company_plot = $obj->con1->prepare("INSERT INTO `pr_company_plots`(`plot_no`, `floor`, `road_no`, `plot_id`, `industrial_estate_id`, `user_id`,`company_id`) VALUES (?,?,?,?,?,?,?)");
  //     $stmt_company_plot->bind_param("ssssiii",$plot_no,$floor_no,$road_no,$plot_id,$industrial_estate_id,$user_id,$last_insert_company_id);
  //     $Resp=$stmt_company_plot->execute();
  //     $stmt_company_plot->close();

  //     if(!$Resp)
  //     {
  //       throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
  //     }
  //   } 
  //   catch(\Exception  $e) {
  //     setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  //   }
  // }
  // else{
  //   // Floor Already Exists
  //   $stmt_plot_list = $obj->con1->prepare("SELECT * FROM `tbl_tdrawdata` WHERE id=?");
  //   $stmt_plot_list->bind_param("i",$plot_rawdata_id);
  //   $stmt_plot_list->execute();
  //   $plot_result = $stmt_plot_list->get_result()->fetch_assoc();
  //   $stmt_plot_list->close();

  //   // get array of plot
  //   $row_data_plot=json_decode($plot_result["raw_data"]);
  //   $plot_array = $row_data_plot->plot_details;

  //   $stmt_company_list = $obj->con1->prepare("SELECT * FROM `tbl_tdrawdata` WHERE id=?");
  //   $stmt_company_list->bind_param("i",$rawdata_id);
  //   $stmt_company_list->execute();
  //   $company_result = $stmt_company_list->get_result()->fetch_assoc();
  //   $stmt_company_list->close();
    
  //   // add array of plot in company json
  //   $row_data_comp=json_decode($company_result["raw_data"]);
  //   $row_data_comp->bad_lead_reason = "";
  //   $row_data_comp->bad_lead_reason_remark = "";
  //   $row_data_comp->Image = "";
  //   $row_data_comp->Constitution = "";
  //   $row_data_comp->Status = $status_company;
  //   $row_data_comp->plot_details = $plot_array;
  //   $row_data_comp->post_fields->IndustrialEstate=$ind_est_result["industrial_estate"];
  //   $row_data_comp->post_fields->state=$ind_est_result["state_id"];
  //   $row_data_comp->post_fields->city=$ind_est_result["city_id"];
  //   $row_data_comp->post_fields->Taluka=$ind_est_result["taluka"];
  //   $row_data_comp->post_fields->Area=$ind_est_result["area_id"];
  //   $row_data_comp->post_fields->Factory_Address=$factory_address;
  //   $row_data_comp->plot_details = Array(
  //           Array(
  //             "Plot_No" => $plot_no,
  //             "Floor" => $floor_no,
  //             "Road_No" => $road_number,
  //             "Plot_Status" => "",
  //             "Plot_Id" => "1",
  //           ));
  //   $json_object = json_encode($row_data_comp);

  //   // get company details for pr_company_details
  //   $post_fields_comp = $row_data_comp->post_fields;
  //   $source = $post_fields_comp->source;
  //   $source_name = $post_fields_comp->Source_Name;
  //   $contact_person = $post_fields_comp->Contact_Name;
  //   $contact_no = $post_fields_comp->Mobile_No;
  //   $firm_name = $post_fields_comp->Firm_Name;
  //   $gst_no = $post_fields_comp->GST_No;
  //   $category = $post_fields_comp->Category;
  //   $segment = $post_fields_comp->Segment;
  //   $premise = $post_fields_comp->Premise;
  //   $state =$ind_est_result["state_id"];
  //   $city = $ind_est_result["city_id"];
  //   $taluka = $ind_est_result["taluka"];
  //   $area = $ind_est_result["area_id"];
  //   $industrial_estate = $ind_est_result["industrial_estate"];
  //   $inq_submit = "Submit";
    
  //   // get id of table pr_company_plot 
  //   if($plotting_pattern=="Series"){
  //     $stmt_company_plot = $obj->con1->prepare("SELECT pid, company_id FROM `pr_company_plots` WHERE plot_no=? and floor=? and industrial_estate_id=? ");
  //     $stmt_company_plot->bind_param("sii",$plot_no,$floor_no,$industrial_estate_id);
  //   }
  //   else if($plotting_pattern=="Road"){
  //     $stmt_company_plot = $obj->con1->prepare("SELECT pid, company_id FROM `pr_company_plots` WHERE plot_no=? and floor=? and industrial_estate_id=? and road_no=?");
  //     $stmt_company_plot->bind_param("siis",$plot_no,$floor_no,$industrial_estate_id,$road_no);
  //   }
  //   $stmt_company_plot->execute();
  //   $pr_company_plot = $stmt_company_plot->get_result()->fetch_assoc();
  //   $stmt_company_plot->close();

  //   try
  //   {
  //     // update json of company
  //     $stmt = $obj->con1->prepare("UPDATE `tbl_tdrawdata` set raw_data=? where id=?");
  //     $stmt->bind_param("si",$json_object,$company_result['id']);
  //     $Resp=$stmt->execute();
  //     $stmt->close();

  //     // delete blank json of plot
  //     $stmt_del = $obj->con1->prepare("DELETE from `tbl_tdrawdata` where id=?");
  //     $stmt_del->bind_param("i",$plot_rawdata_id);
  //     $Resp=$stmt_del->execute();
  //     $stmt_del->close();

  //     // insert into pr_company_details and pr_company_plots
  //     $stmt_pr_company_detail = $obj->con1->prepare("INSERT INTO `pr_company_details`(`source`, `source_name`, `contact_name`, `mobile_no`, `firm_name`, `gst_no`, `category`, `segment`, `premise`, `state`, `city`, `taluka`, `area`, `industrial_estate`, `inq_submit`, `industrial_estate_id`, `user_id`, `rawdata_id`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
  //     $stmt_pr_company_detail->bind_param("sssssssssssssssiiis",$source,$source_name,$contact_person,$contact_no,$firm_name,$gst_no,$category,$segment,$premise,$state,$city,$taluka,$area,$industrial_estate,$inq_submit,$industrial_estate_id,$user_id,$company_result['id'],$status_company);
  //     $Resp=$stmt_pr_company_detail->execute();
  //     $last_insert_company_id = mysqli_insert_id($obj->con1);
  //     $stmt_pr_company_detail->close();
      
  //     $stmt_pr_company_plot = $obj->con1->prepare("UPDATE `pr_company_plots` SET `company_id`=? WHERE `pid`=?");
  //     $stmt_pr_company_plot->bind_param("ii",$last_insert_company_id,$pr_company_plot['pid']);
  //     $Resp=$stmt_pr_company_plot->execute();
  //     $stmt_pr_company_plot->close();

  //     if(!$Resp)
  //     {
  //       throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
  //     }
  //   } 
  //   catch(\Exception  $e) {
  //     setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  //   }
  // }

  // //update tbl_tdcompany if not in lead/badlead
  // if($status_company!='Positive' && $status_company!='Negative')
  // {
  //   $stmt_comp_data = $obj->con1->prepare("SELECT * FROM `tbl_tdcompany` WHERE inq_id=?");
  //   $stmt_comp_data->bind_param("i",$rawdata_id);
  //   $stmt_comp_data->execute();
  //   $comp_result = $stmt_comp_data->get_result()->fetch_assoc();
  //   $stmt_comp_data->close();
  //   $comp_data=json_decode($comp_result["company_data"]);
  //   $comp_data->IndustrialEstate=$ind_est_result["industrial_estate"];
  //   $comp_data->Area=$ind_est_result["area_id"];
  //   $comp_data->Company_Address=$factory_address;
  //   $json_object_comp = json_encode($comp_data);

  //   //update tbl_tdcompany
  //   $stmt_tdcompany = $obj->con1->prepare("UPDATE `tbl_tdcompany` SET `company_data`=? WHERE `inq_id`=?");
  //   $stmt_tdcompany->bind_param("si",$json_object_comp,$rawdata_id);
  //   $Resp=$stmt_tdcompany->execute();
  //   $stmt_tdcompany->close();

  //   //tbl_tdapplication
  //   $stmt_app_data = $obj->con1->prepare("SELECT * FROM `tbl_tdapplication` WHERE inq_id=?");
  //   $stmt_app_data->bind_param("i",$rawdata_id);
  //   $stmt_app_data->execute();
  //   $app_result = $stmt_app_data->get_result()->fetch_assoc();
  //   $stmt_app_data->close();
  //   $app_data=json_decode($comp_result["app_data"]);
  //   $app_data->company_details->IndustrialEstate=$ind_est_result["industrial_estate"];
  //   $app_data->company_details->Area=$area_comp;
  //   $json_object_app = json_encode($app_data);

  //   //update tbl_tdapplication
  //   $stmt_tdcompany = $obj->con1->prepare("UPDATE `tbl_tdapplication` SET `app_data`=? WHERE `inq_id`=?");
  //   $stmt_tdcompany->bind_param("si",$json_object_app,$rawdata_id);
  //   $Resp=$stmt_tdcompany->execute();
  //   $stmt_tdcompany->close();


  // }
  
  if($resp_update)
  {
    

    setcookie("msg", "update",time()+3600,"/");
    header("location:company_plot_report.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:edit_plot.php");
  }
}



?>

<h4 class="fw-bold py-3 mb-4">Edit Plot In Company</h4>

<?php 
if(isset($_COOKIE["msg"]) )
{

  if($_COOKIE['msg']=="data")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data added succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="update")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data updated succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="data_del")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data deleted succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="fail")
  {
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
  if(isset($_COOKIE["sql_error"]))
  {
    ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <?php echo urldecode($_COOKIE['sql_error'])?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>

    <script type="text/javascript">eraseCookie("sql_error")</script>
    <?php
  }
?>


  <!-- Basic Layout -->
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-body">
          <form method="post" >
           
            <!-- div for selecting estate first -->
            <div >
              <div class="mb-3">
                <label class="form-label" for="firm_name">Firm Name</label>
                <input type="text" name="firm_name" class="form-control" value="<?php echo $post_fields["Firm_Name"] ?>">
              </div>
              <div class="mb-3">
                <label class="form-label" for="gst_no">GST No.</label>
                <input type="text" name="gst_no" class="form-control" value="<?php echo $post_fields["GST_No"] ?>" onkeyup="checkGST(this.value,<?php echo $estate['id']?>)">
                <div id="gst_alert" class="text-danger"></div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="plot_status">Plot Status</label>
                
                <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="plot_status" id="Open_Plot" value="Open Plot"
                  <?php echo ($plot_status == "Open Plot") ? "checked" : "" ?>>
                <label class="form-check-label" for="inlineRadio1">Open Plot</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="plot_status" id="Under_Construction" value="Under Construction"
                  <?php echo ($plot_status == "Under Construction") ? "checked" : "" ?>>
                <label class="form-check-label" for="inlineRadio1">Under Construction</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="plot_status" id="constructed" value="Constructed"
                  <?php echo ($plot_status == "Constructed") ? "checked" : "" ?>>
                <label class="form-check-label" for="inlineRadio1">Constructed</label>
              </div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control" value="<?php echo $post_fields["Contact_Name"] ?>">
              </div>
              <div class="mb-3">
                <label class="form-label" for="contact">Contact No.</label>
                <input type="text" name="contact" class="form-control" value="<?php echo $post_fields["Mobile_No"] ?>">
              </div>
              <div class="mb-3">
                <label class="form-label " for="stats">Status</label>
                
               
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="status" id="existing_client" value="Existing Client"
                  <?php echo ($data["Status"] == "Existing Client") ? "checked" : "" ?>>
                <label class="form-check-label" for="inlineRadio1">Existing Client</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="status" id="positive" value="Positive" <?php echo ($data["Status"]== "Positive") ? "checked" : "" ?>>
                <label class="form-check-label" for="inlineRadio1">Positive</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="status" id="negative" value="Negative" <?php echo ($data["Status"] == "Negative") ? "checked" : "" ?>>
                <label class="form-check-label" for="inlineRadio1">Negative</label>
              </div>
            </div>
              </div>

              
              <div class="mb-3">
                <label class="form-label" for="constitution">Constitution</label>
                
                <select name="constitution" id="constitution" class="form-control"  >
                    <option value="">Select Constitution</option>
                    <option value="PRIVATE LIMITED" <?php echo ($data["Constitution"]=="PRIVATE LIMITED")?"selected":"" ?>>PRIVATE LIMITED</option>
                    <option value="LIMITED LIABILITY PARTNERSHIP(LLP)" <?php echo ($data["Constitution"]=="LIMITED LIABILITY PARTNERSHIP(LLP)")?"selected":"" ?>>LIMITED LIABILITY PARTNERSHIP(LLP)</option>
                    <option value="PARTNERSHIP" <?php echo ($data["Constitution"]=="PARTNERSHIP")?"selected":"" ?>>PARTNERSHIP</option>
                    <option value="PROPRIETORSHIP" <?php echo ($data["Constitution"]=="PROPRIETORSHIP")?"selected":"" ?>>PROPRIETORSHIP</option>
                </select>

              </div>
              <div class="mb-3">
                <label class="form-label" for="remark">Remark</label>
                <input type="text" name="remark" class="form-control" value="<?php echo $post_fields["Remarks"] ?>" >
              </div>
              <div class="mb-3">
                <label class="form-label" for="segment">Segment</label>
                
                <select name="segment" id="segment" class="form-control"  >
                    <option value="">Select segment</option>
                    <option value="Weaving (Power Looms)" <?php echo ($post_fields["Segment"]=="Weaving (Power Looms)")?"selected":"" ?>>Weaving (Power Looms)</option>
                    <option value="Weaving (Airjet Looms)" <?php echo ($post_fields["Segment"]=="Weaving (Airjet Looms)")?"selected":"" ?>>Weaving (Airjet Looms)</option>
                    <option value="Weaving (Waterjet Looms)" <?php echo ($post_fields["Segment"]=="Weaving (Waterjet Looms)")?"selected":"" ?>>Weaving (Waterjet Looms)</option>
                    <option value="Weaving (Rapier Looms)" <?php echo ($post_fields["Segment"]=="Weaving (Rapier Looms)")?"selected":"" ?>>Weaving (Rapier Looms)</option>
                    <option value="Embroidey Machine(Stand Alone)" <?php echo ($post_fields["Segment"]=="Embroidey Machine(Stand Alone)")?"selected":"" ?>>Embroidey Machine(Stand Alone)</option>
                </select>
              </div>

              <button type="submit" name="btn_update_plot" id="btn_update_plot" class="btn btn-primary" >Update</button>
              <button name="btncancel" id="btncancel" class="btn btn-secondary" onclick="closeNewTabAndReturn()">Cancel</button>


              

          </form>
        </div>
      </div>
    </div>
    
  </div>


<!-- / Content -->
<script type="text/javascript">

function closeNewTabAndReturn() {
  
  // Close the new tab
  //window.close();
  // Focus back to the opener window (the previous tab)
 // window.opener.focus();
 history.back();
  
}

function checkGST(gst_no,id)
  {
   
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=checkGST",
      data: "gst_no="+gst_no+"&id="+id,
      cache: false,
      success: function(result){
        var resp=result.split("@@@@@");
        if(resp[0]>0)
        {
          $('#gst_alert').html('GST No. already exist!');
          document.getElementById('btn_update_plot').disabled = true;
         
        }
        else
        {
          $('#gst_alert').html('');
          document.getElementById('btn_update_plot').disabled = false;
          
        }
      }
    });
  }

$( document ).ready(function() {
  
  
  
});


  
  function changeForm() {
    if($('#select_company_first').is(':checked')) { 
      $('#select_company_first_div').removeAttr('hidden');
      $('#select_estate_first_div').attr('hidden',true);
      $("[id$='_comp']:not([id='road_no_comp'])").attr("required", true);
      $("[id$='_est']").removeAttr('required');
    }
    else if($('#select_estate_first').is(':checked')) { 
      $('#select_estate_first_div').removeAttr('hidden');
      $('#select_company_first_div').attr('hidden',true);
      $("[id$='_est']:not([id='road_no_est'])").attr("required", true);
      $("[id$='_comp']").removeAttr('required');
    }
  }

  function getPlot_companyPlot_est(estate_id){
    if(estate_id!=""){
      $.ajax({
        async: false,
        type: "POST",
        url: "ajaxdata.php?action=getPlot_companyPlot_est",
        data: "estate_id="+estate_id,
        cache: false,
        success: function(result){
          var data = result.split("@@@@@");
          $('#plotting_div_est').html('');
          $('#plotting_div_est').html(data[0]);
          if(data[1]==true){
            document.getElementById('btnsubmit_est').disabled = false;
          }else{
            document.getElementById('btnsubmit_est').disabled = true;
          }
        }
      });
    }
  }

  function getPlot_companyPlot_comp(estate_id){
    if(estate_id!=""){
      $.ajax({
        async: false,
        type: "POST",
        url: "ajaxdata.php?action=getPlot_companyPlot_comp",
        data: "estate_id="+estate_id,
        cache: false,
        success: function(result){
          console.log(result);
          var data = result.split("@@@@@");
          $('#plotting_div_comp').html('');
          $('#plotting_div_comp').html(data[0]);
          if(data[1]==true){
            document.getElementById('btnsubmit_comp').disabled = false;
          }else{
            document.getElementById('btnsubmit_comp').disabled = true;
          }
        }
      });
    }
  }

  function getRoadPlots_companyPlot(road_no,estate_id){
    if(road_no!=""){
      if(readCookie("selecttype_comp_addplot")=="select_company_first") { 
        suffix = '_comp';
      }
      else if(readCookie("selecttype_comp_addplot")=="select_estate_first") { 
        suffix = '_est';
      }
      $.ajax({
        async: false,
        type: "POST",
        url: "ajaxdata.php?action=getRoadPlots_companyPlot",
        data: "road_no="+road_no+"&estate_id="+estate_id,
        cache: false,
        success: function(result){
          $('#plot_no'+suffix).html('');
          $('#plot_no'+suffix).append(result);
          $('#floor'+suffix).html('');
          $('#floor'+suffix).append('<option value="">Select Floor No.</option>');
        }
      });
    }
    else{
      $('#floor'+suffix).html('');
      $('#floor'+suffix).append('<option value="">Select Floor No.</option>');
    }
  }

  function getFloor_companyPlot(plot_no,estate_id){

    if(plot_no!=""){
      if(readCookie("selecttype_comp_addplot")=="select_company_first") { 
        suffix = '_comp';
      }
      else if(readCookie("selecttype_comp_addplot")=="select_estate_first") { 
        suffix = '_est';
      }
      road_no = $('#road_no'+suffix).val();
      $.ajax({
        async: false,
        type: "POST",
        url: "ajaxdata.php?action=getFloor_companyPlot",
        data: "plot_no="+plot_no+"&estate_id="+estate_id+"&road_no="+road_no,
        cache: false,
        success: function(result){
          $('#floor'+suffix).html('');
          $('#floor'+suffix).append(result);
        }
      });
    }
    else{
      $('#floor'+suffix).html('');
      $('#floor'+suffix).append('<option value="">Select Floor No.</option>');
    }
  }

  
</script>
<?php 
  include("footer.php");
?>