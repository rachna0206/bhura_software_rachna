<?php
  include("header.php");

$user_id = $_SESSION["id"];

// estate data
$stmt_est = $obj->con1->prepare("select * from tbl_industrial_estate e1,pr_add_industrialestate_details p1 where  p1.industrial_estate_id=e1.id and  e1.id=852");
$stmt_est->execute();
$est_result = $stmt_est->get_result()->fetch_assoc();
$stmt_est->close();

//estate roadplot data
$stmt_est_roadplot = $obj->con1->prepare("SELECT * FROM `pr_estate_roadplot` where industrial_estate_id=852; ");
$stmt_est_roadplot->execute();
$est_roadplot_result = $stmt_est_roadplot->get_result()->fetch_assoc();
$stmt_est_roadplot->close();

//pr_company_plots data
$stmt_pr_comp_plot = $obj->con1->prepare("SELECT * FROM `pr_company_plots` where industrial_estate_id=852; ");
$stmt_pr_comp_plot->execute();
$stmt_pr_comp_plot_result = $stmt_pr_comp_plot->get_result();
$stmt_pr_comp_plot->close();
$num_plots=$stmt_pr_comp_plot_result->num_rows;

$taluka=$est_result["taluka"];  
$area=$est_result["area_id"];
$state = $est_result["state_id"];
$city = $est_result["city_id"];

$industrial_estate = $est_result["industrial_estate"];
$plotting_pattern = $est_result["plotting_pattern"];
$verify_status = "Verified";

$floor_no = "0";
$plot_id = "1";

if($est_result["plotting_pattern"]=='Series'){

    $stmt_series_plot = $obj->con1->prepare("SELECT * FROM `pr_company_plots` where industrial_estate_id=852 and  plot_end_no!=NULL");
    $stmt_series_plot->execute();
    $stmt_series_plot_result = $stmt_series_plot->get_result()->fetch_assoc();
    $stmt_series_plot->close();


  $from_plotno = $est_roadplot_result["plot_start_no"];
  $to_plotno = $est_roadplot_result["plot_end_no"];  
  $series_plot_cnt = $num_plots;
}
else if($est_result["plotting_pattern"]=='Road'){
//   $num_of_roads = $_REQUEST['road_cnt'];
//   $num_of_additional_roads = $_REQUEST['additional_road_cnt'];
//   $from_roadno = $_REQUEST['from_roadno'];
//   $to_roadno = $_REQUEST['to_roadno'];  
}

$description='';

try
{

  // if plotting pattern = Series
  if($plotting_pattern=='Series'){
   
    //check plot/estate entry in tdrawdata
    $stmt_plot =  $obj->con1->prepare("SELECT * FROM tbl_tdrawdata WHERE lower(raw_data->'$.post_fields.Taluka') like '%" . strtolower($taluka) . "%' and lower(raw_data->'$.post_fields.IndustrialEstate') like '%" . strtolower($industrial_estate) . "%' and lower(raw_data->'$.post_fields.Area') like '%" . strtolower($area) . "%'");
    $stmt_plot->execute();
    $plot_res = $stmt_plot->get_result();
    $stmt_plot->close();

    $estate = mysqli_fetch_array($plot_res);
    // Decode JSON into a PHP associative array
    $data = json_decode($estate["raw_data"], true);

    while($plots=mysqli_fetch_array($stmt_pr_comp_plot_result)){

        
      

      $cp = Array (
          "post_fields" => Array (
          "source" => "",
          "Source_Name" => "",
          "Contact_Name" => "",
          "Mobile_No" => "",
          "Email" => "",
          "Designation_In_Firm" => "",
          "Firm_Name" => "",
          "GST_No" => "",
          "Type_of_Company" => "",
          "Category" => "",
          "Segment" => "",
          "Premise" => "",
          "Factory_Address" => "",
          "state" => $state,
          "city" => $city,
          "Taluka" => $taluka,
          "Area" => $area,
          "IndustrialEstate" => $industrial_estate,
          "loan_applied" => "",
          "Completion_Date" => "",
          "Term_Loan_Amount" => "",
          "CC_Loan_Amount" => "",
          "Under_Process_Bank" => "",
          "Under_Process_Branch" => "",
          "Term_Loan_Amount_In_Process" => "",
          "Under_Process_Date" => "",
          "ROI" => "",
          "Colletral" => "",
          "Consultant" => "",
          "Sanctioned_Bank" => "",
          "Bank_Branch" => "",
          "DOS" => "",
          "TL_Amount" => "",
          "Sactioned_Loan_Consultant" => "",
          "category_type" => "",
          "Remarks" => ""
        ),
        "inq_submit" => "Submit",
        "bad_lead_reason" => "",
        "bad_lead_reason_remark" => "",
        "Image" => "",
        "Constitution" => "",
        "Status" => "",
        "plot_details" => Array(
          Array(
          "Plot_No" => $plots["plot_no"],
          "Floor" => $plots["floor"],
          "Road_No" =>$plots["road_no"],
          "Plot_Status" => $plots["plot_status"],
          "Plot_Id" => $plots["plot_id"],
          ),
        ) 
      );

      // $plot_details=Array(
      //   Array(
      //   "Plot_No" => $plots["plot_no"],
      //   "Floor" => $plots["floor"],
      //   "Road_No" =>$plots["road_no"],
      //   "Plot_Status" => $plots["plot_status"],
      //   "Plot_Id" => $plots["plot_id"],
      //   ),
      // ) ;

      // $data['plot_details'] = $plot_details;
       
      // Encode array to json
     // $json = json_encode($data);
      $json = json_encode($cp);
       
      // Display it
      echo "<br>=".$json;

      //update json instead of insert
      //$Resp = tbl_tdrawdata_insert($json,$user_id);


      $road_number = NULL;

     
    }

    
  }

  // if plotting pattern = Road
  // else if($plotting_pattern=='Road'){
  //   for($i=0;$i<=$num_of_roads;$i++){

  //     $road_number = $_REQUEST['road_no'.$i];
  //     $road_plot_cnt = $_REQUEST['road_plot_cnt'.$i];
  //     $from_to_plot_cnt = $_REQUEST['from_to_plot_cnt'.$i];

  //     for($ft=0;$ft<$from_to_plot_cnt;$ft++){
  //       $from_plotno_road = strtoupper($_REQUEST['from_plotno_road'.$i.'_'.$ft]);
  //       $to_plotno_road = strtoupper($_REQUEST['to_plotno_road'.$i.'_'.$ft]);

  //       $letters = "/^[A-Za-z]$/";
  //       $suffix = "/^[0-9]+[^a-zA-Z0-9]*[a-zA-Z]$/";
  //       $prefix = "/^[a-zA-Z][^a-zA-Z0-9]*[0-9]+$/";
  //       $specialChars ="/[`!@#$%^&*()_\-+=\[\]{};':\\|,.<>\/?~ ]+/";
  //       $re_for_alphabet = "/([a-zA-Z]+)/";
  //       $re_for_digits = "/(\d+)/";

  //       if($from_plotno_road!="" && $from_plotno_road!=null){

  //         $stmt_plot = $obj->con1->prepare("INSERT INTO `pr_estate_roadplot`(`industrial_estate_id`, `road_no`, `plot_start_no`, `plot_end_no`, `user_id`) VALUES (?,?,?,?,?)");
  //         $stmt_plot->bind_param("isssi",$insert_id,$road_number,$from_plotno_road,$to_plotno_road,$user_id);
  //         $Resp=$stmt_plot->execute();
  //         $stmt_plot->close();

  //         if(is_numeric($from_plotno_road) && is_numeric($to_plotno_road)){
  //           $type = "numeric";  
  //         }
  //         else if(preg_match($letters,$from_plotno_road) && preg_match($letters,$to_plotno_road)){
  //           $type = "alphabet";
  //         }
  //         else if(preg_match($prefix,$from_plotno_road) && preg_match($prefix,$to_plotno_road)){
  //           $type = "prefix";
  //         }
  //         else if(preg_match($suffix,$from_plotno_road) && preg_match($suffix,$to_plotno_road)){
  //           $type = "suffix";
  //         }


  //         if($type=="numeric"){
  //           for($p=$from_plotno_road;$p<=$to_plotno_road;$p++){
  //             $cp = Array (
  //                 "post_fields" => Array (
  //                 "source" => "",
  //                 "Source_Name" => "",
  //                 "Contact_Name" => "",
  //                 "Mobile_No" => "",
  //                 "Email" => "",
  //                 "Designation_In_Firm" => "",
  //                 "Firm_Name" => "",
  //                 "GST_No" => "",
  //                 "Type_of_Company" => "",
  //                 "Category" => "",
  //                 "Segment" => "",
  //                 "Premise" => "",
  //                 "Factory_Address" => "",
  //                 "state" => $state,
  //                 "city" => $city,
  //                 "Taluka" => $taluka,
  //                 "Area" => $area,
  //                 "IndustrialEstate" => $industrial_estate,
  //                 "loan_applied" => "",
  //                 "Completion_Date" => "",
  //                 "Term_Loan_Amount" => "",
  //                 "CC_Loan_Amount" => "",
  //                 "Under_Process_Bank" => "",
  //                 "Under_Process_Branch" => "",
  //                 "Term_Loan_Amount_In_Process" => "",
  //                 "Under_Process_Date" => "",
  //                 "ROI" => "",
  //                 "Colletral" => "",
  //                 "Consultant" => "",
  //                 "Sanctioned_Bank" => "",
  //                 "Bank_Branch" => "",
  //                 "DOS" => "",
  //                 "TL_Amount" => "",
  //                 "Sactioned_Loan_Consultant" => "",
  //                 "category_type" => "",
  //                 "Remarks" => ""
  //               ),
  //               "inq_submit" => "Submit",
  //               "bad_lead_reason" => "",
  //               "bad_lead_reason_remark" => "",
  //               "Image" => "",
  //               "Constitution" => "",
  //               "Status" => "",
  //               "plot_details" => Array(
  //                 Array(
  //                 "Plot_No" => $p,
  //                 "Floor" => "0",
  //                 "Road_No" => $road_number,
  //                 "Plot_Status" => "",
  //                 "Plot_Id" => "1",
  //                 ),
  //               ) 
  //             );
               
  //             // Encode array to json
  //             $json = json_encode($cp);
               
  //             // Display it
  //             //echo "$json";

  //             $Resp = tbl_tdrawdata_insert($json,$user_id);
              
  //             company_plot_insert($p,$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //           }
  //         }
  //         else if($type=="alphabet") {
  //           $from_plot_upper = strtoupper($from_plotno_road);
  //           $to_plot_upper = strtoupper($to_plotno_road);
  //           $from_plot_ascii = ord($from_plot_upper);
  //           $to_plot_ascii = ord($to_plot_upper);
  //           for($p=$from_plot_ascii;$p<=$to_plot_ascii;$p++){
  //             $cp = Array (
  //                 "post_fields" => Array (
  //                 "source" => "",
  //                 "Source_Name" => "",
  //                 "Contact_Name" => "",
  //                 "Mobile_No" => "",
  //                 "Email" => "",
  //                 "Designation_In_Firm" => "",
  //                 "Firm_Name" => "",
  //                 "GST_No" => "",
  //                 "Type_of_Company" => "",
  //                 "Category" => "",
  //                 "Segment" => "",
  //                 "Premise" => "",
  //                 "Factory_Address" => "",
  //                 "state" => $state,
  //                 "city" => $city,
  //                 "Taluka" => $taluka,
  //                 "Area" => $area,
  //                 "IndustrialEstate" => $industrial_estate,
  //                 "loan_applied" => "",
  //                 "Completion_Date" => "",
  //                 "Term_Loan_Amount" => "",
  //                 "CC_Loan_Amount" => "",
  //                 "Under_Process_Bank" => "",
  //                 "Under_Process_Branch" => "",
  //                 "Term_Loan_Amount_In_Process" => "",
  //                 "Under_Process_Date" => "",
  //                 "ROI" => "",
  //                 "Colletral" => "",
  //                 "Consultant" => "",
  //                 "Sanctioned_Bank" => "",
  //                 "Bank_Branch" => "",
  //                 "DOS" => "",
  //                 "TL_Amount" => "",
  //                 "Sactioned_Loan_Consultant" => "",
  //                 "category_type" => "",
  //                 "Remarks" => ""
  //               ),
  //               "inq_submit" => "Submit",
  //               "bad_lead_reason" => "",
  //               "bad_lead_reason_remark" => "",
  //               "Image" => "",
  //               "Constitution" => "",
  //               "Status" => "",
  //               "plot_details" => Array(
  //                 Array(
  //                 "Plot_No" => chr($p),
  //                 "Floor" => "0",
  //                 "Road_No" => $road_number,
  //                 "Plot_Status" => "",
  //                 "Plot_Id" => "1",
  //                 ),
  //               ) 
  //             );
               
  //             // Encode array to json
  //             $json = json_encode($cp);
               
  //             // Display it
  //             //echo "$json";

  //             $Resp = tbl_tdrawdata_insert($json,$user_id);
              
  //             company_plot_insert(chr($p),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //           }
  //         }
  //         else if($type=="prefix") {

  //           preg_match($re_for_digits,$from_plotno_road,$from_plot_number);
  //           preg_match($re_for_alphabet,$from_plotno_road,$from_plot_alphabet);
  //           preg_match($specialChars,$from_plotno_road,$from_plot_char);

  //           preg_match($re_for_digits,$to_plotno_road,$to_plot_number);
  //           preg_match($re_for_alphabet,$to_plotno_road,$to_plot_alphabet);
  //           preg_match($specialChars,$to_plotno_road,$to_plot_char);

  //           if($to_plot_number[0]>=$from_plot_number[0] && $to_plot_alphabet[0]==$from_plot_alphabet[0]){
  //             // number increment
  //             for($p=$from_plot_number[0];$p<=$to_plot_number[0];$p++){
  //               $cp = Array (
  //                   "post_fields" => Array (
  //                   "source" => "",
  //                   "Source_Name" => "",
  //                   "Contact_Name" => "",
  //                   "Mobile_No" => "",
  //                   "Email" => "",
  //                   "Designation_In_Firm" => "",
  //                   "Firm_Name" => "",
  //                   "GST_No" => "",
  //                   "Type_of_Company" => "",
  //                   "Category" => "",
  //                   "Segment" => "",
  //                   "Premise" => "",
  //                   "Factory_Address" => "",
  //                   "state" => $state,
  //                   "city" => $city,
  //                   "Taluka" => $taluka,
  //                   "Area" => $area,
  //                   "IndustrialEstate" => $industrial_estate,
  //                   "loan_applied" => "",
  //                   "Completion_Date" => "",
  //                   "Term_Loan_Amount" => "",
  //                   "CC_Loan_Amount" => "",
  //                   "Under_Process_Bank" => "",
  //                   "Under_Process_Branch" => "",
  //                   "Term_Loan_Amount_In_Process" => "",
  //                   "Under_Process_Date" => "",
  //                   "ROI" => "",
  //                   "Colletral" => "",
  //                   "Consultant" => "",
  //                   "Sanctioned_Bank" => "",
  //                   "Bank_Branch" => "",
  //                   "DOS" => "",
  //                   "TL_Amount" => "",
  //                   "Sactioned_Loan_Consultant" => "",
  //                   "category_type" => "",
  //                   "Remarks" => ""
  //                 ),
  //                 "inq_submit" => "Submit",
  //                 "bad_lead_reason" => "",
  //                 "bad_lead_reason_remark" => "",
  //                 "Image" => "",
  //                 "Constitution" => "",
  //                 "Status" => "",
  //                 "plot_details" => Array(
  //                   Array(
  //                   "Plot_No" => strtoupper($from_plot_alphabet[0]).$from_plot_char[0].$p,
  //                   "Floor" => "0",
  //                   "Road_No" => $road_number,
  //                   "Plot_Status" => "",
  //                   "Plot_Id" => "1",
  //                   ),
  //                 ) 
  //               );
                 
  //               // Encode array to json
  //               $json = json_encode($cp);
                 
  //               // Display it
  //               //echo "$json";

  //               $Resp = tbl_tdrawdata_insert($json,$user_id);
                
  //               company_plot_insert((strtoupper($from_plot_alphabet[0]).$from_plot_char[0].$p),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //             }
  //           }
  //           else if($to_plot_number[0]==$from_plot_number[0] && strtoupper($to_plot_alphabet[0])>=strtoupper($from_plot_alphabet[0])){
  //             // alphabet increment
  //             for($p=ord(strtoupper($from_plot_alphabet[0]));$p<=ord(strtoupper($to_plot_alphabet[0]));$p++){
  //               $cp = Array (
  //                   "post_fields" => Array (
  //                   "source" => "",
  //                   "Source_Name" => "",
  //                   "Contact_Name" => "",
  //                   "Mobile_No" => "",
  //                   "Email" => "",
  //                   "Designation_In_Firm" => "",
  //                   "Firm_Name" => "",
  //                   "GST_No" => "",
  //                   "Type_of_Company" => "",
  //                   "Category" => "",
  //                   "Segment" => "",
  //                   "Premise" => "",
  //                   "Factory_Address" => "",
  //                   "state" => $state,
  //                   "city" => $city,
  //                   "Taluka" => $taluka,
  //                   "Area" => $area,
  //                   "IndustrialEstate" => $industrial_estate,
  //                   "loan_applied" => "",
  //                   "Completion_Date" => "",
  //                   "Term_Loan_Amount" => "",
  //                   "CC_Loan_Amount" => "",
  //                   "Under_Process_Bank" => "",
  //                   "Under_Process_Branch" => "",
  //                   "Term_Loan_Amount_In_Process" => "",
  //                   "Under_Process_Date" => "",
  //                   "ROI" => "",
  //                   "Colletral" => "",
  //                   "Consultant" => "",
  //                   "Sanctioned_Bank" => "",
  //                   "Bank_Branch" => "",
  //                   "DOS" => "",
  //                   "TL_Amount" => "",
  //                   "Sactioned_Loan_Consultant" => "",
  //                   "category_type" => "",
  //                   "Remarks" => ""
  //                 ),
  //                 "inq_submit" => "Submit",
  //                 "bad_lead_reason" => "",
  //                 "bad_lead_reason_remark" => "",
  //                 "Image" => "",
  //                 "Constitution" => "",
  //                 "Status" => "",
  //                 "plot_details" => Array(
  //                   Array(
  //                   "Plot_No" => chr($p).$from_plot_char[0].$from_plot_number[0],
  //                   "Floor" => "0",
  //                   "Road_No" => $road_number,
  //                   "Plot_Status" => "",
  //                   "Plot_Id" => "1",
  //                   ),
  //                 ) 
  //               );
                 
  //               // Encode array to json
  //               $json = json_encode($cp);
                 
  //               // Display it
  //               //echo "$json";

  //               $Resp = tbl_tdrawdata_insert($json,$user_id);
                
  //               company_plot_insert((chr($p).$from_plot_char[0].$from_plot_number[0]),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //             }
  //           }
  //         }
  //         else if($type=="suffix") {
  //           preg_match($re_for_digits,$from_plotno_road,$from_plot_number);
  //           preg_match($re_for_alphabet,$from_plotno_road,$from_plot_alphabet);
  //           preg_match($specialChars,$from_plotno_road,$from_plot_char);

  //           preg_match($re_for_digits,$to_plotno_road,$to_plot_number);
  //           preg_match($re_for_alphabet,$to_plotno_road,$to_plot_alphabet);
  //           preg_match($specialChars,$to_plotno_road,$to_plot_char);

  //           if($to_plot_number[0]>=$from_plot_number[0] && $to_plot_alphabet[0]==$from_plot_alphabet[0]){
  //             // number increment
  //             for($p=$from_plot_number[0];$p<=$to_plot_number[0];$p++){
  //               $cp = Array (
  //                   "post_fields" => Array (
  //                   "source" => "",
  //                   "Source_Name" => "",
  //                   "Contact_Name" => "",
  //                   "Mobile_No" => "",
  //                   "Email" => "",
  //                   "Designation_In_Firm" => "",
  //                   "Firm_Name" => "",
  //                   "GST_No" => "",
  //                   "Type_of_Company" => "",
  //                   "Category" => "",
  //                   "Segment" => "",
  //                   "Premise" => "",
  //                   "Factory_Address" => "",
  //                   "state" => $state,
  //                   "city" => $city,
  //                   "Taluka" => $taluka,
  //                   "Area" => $area,
  //                   "IndustrialEstate" => $industrial_estate,
  //                   "loan_applied" => "",
  //                   "Completion_Date" => "",
  //                   "Term_Loan_Amount" => "",
  //                   "CC_Loan_Amount" => "",
  //                   "Under_Process_Bank" => "",
  //                   "Under_Process_Branch" => "",
  //                   "Term_Loan_Amount_In_Process" => "",
  //                   "Under_Process_Date" => "",
  //                   "ROI" => "",
  //                   "Colletral" => "",
  //                   "Consultant" => "",
  //                   "Sanctioned_Bank" => "",
  //                   "Bank_Branch" => "",
  //                   "DOS" => "",
  //                   "TL_Amount" => "",
  //                   "Sactioned_Loan_Consultant" => "",
  //                   "category_type" => "",
  //                   "Remarks" => ""
  //                 ),
  //                 "inq_submit" => "Submit",
  //                 "bad_lead_reason" => "",
  //                 "bad_lead_reason_remark" => "",
  //                 "Image" => "",
  //                 "Constitution" => "",
  //                 "Status" => "",
  //                 "plot_details" => Array(
  //                   Array(
  //                   "Plot_No" => $p.$from_plot_char[0].strtoupper($from_plot_alphabet[0]),
  //                   "Floor" => "0",
  //                   "Road_No" => $road_number,
  //                   "Plot_Status" => "",
  //                   "Plot_Id" => "1",
  //                   ),
  //                 ) 
  //               );
                 
  //               // Encode array to json
  //               $json = json_encode($cp);
                 
  //               // Display it
  //               //echo "$json";

  //               $Resp = tbl_tdrawdata_insert($json,$user_id);
                
  //               company_plot_insert(($p.$from_plot_char[0]).strtoupper($from_plot_alphabet[0]),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //             }
  //           }
  //           else if($to_plot_number[0]==$from_plot_number[0] && strtoupper($to_plot_alphabet[0])>=strtoupper($from_plot_alphabet[0])){
  //             // alphabet increment
  //             for($p=ord(strtoupper($from_plot_alphabet[0]));$p<=ord(strtoupper($to_plot_alphabet[0]));$p++){
  //               $cp = Array (
  //                   "post_fields" => Array (
  //                   "source" => "",
  //                   "Source_Name" => "",
  //                   "Contact_Name" => "",
  //                   "Mobile_No" => "",
  //                   "Email" => "",
  //                   "Designation_In_Firm" => "",
  //                   "Firm_Name" => "",
  //                   "GST_No" => "",
  //                   "Type_of_Company" => "",
  //                   "Category" => "",
  //                   "Segment" => "",
  //                   "Premise" => "",
  //                   "Factory_Address" => "",
  //                   "state" => $state,
  //                   "city" => $city,
  //                   "Taluka" => $taluka,
  //                   "Area" => $area,
  //                   "IndustrialEstate" => $industrial_estate,
  //                   "loan_applied" => "",
  //                   "Completion_Date" => "",
  //                   "Term_Loan_Amount" => "",
  //                   "CC_Loan_Amount" => "",
  //                   "Under_Process_Bank" => "",
  //                   "Under_Process_Branch" => "",
  //                   "Term_Loan_Amount_In_Process" => "",
  //                   "Under_Process_Date" => "",
  //                   "ROI" => "",
  //                   "Colletral" => "",
  //                   "Consultant" => "",
  //                   "Sanctioned_Bank" => "",
  //                   "Bank_Branch" => "",
  //                   "DOS" => "",
  //                   "TL_Amount" => "",
  //                   "Sactioned_Loan_Consultant" => "",
  //                   "category_type" => "",
  //                   "Remarks" => ""
  //                 ),
  //                 "inq_submit" => "Submit",
  //                 "bad_lead_reason" => "",
  //                 "bad_lead_reason_remark" => "",
  //                 "Image" => "",
  //                 "Constitution" => "",
  //                 "Status" => "",
  //                 "plot_details" => Array(
  //                   Array(
  //                   "Plot_No" => $from_plot_number[0].$from_plot_char[0].chr($p),
  //                   "Floor" => "0",
  //                   "Road_No" => $road_number,
  //                   "Plot_Status" => "",
  //                   "Plot_Id" => "1",
  //                   ),
  //                 ) 
  //               );
                 
  //               // Encode array to json
  //               $json = json_encode($cp);
                 
  //               // Display it
  //               //echo "$json";

  //               $Resp = tbl_tdrawdata_insert($json,$user_id);
                
  //               company_plot_insert(($from_plot_number[0].$from_plot_char[0].chr($p)),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //             }
  //           }
  //         }
  //       }
  //     }
      
        
  //     if($from_plotno_road!="" && $from_plotno_road!=null){
  //       // for additional plot in road wise
  //       if($road_plot_cnt>0){
  //         for($c=0;$c<$road_plot_cnt;$c++){
  //           $additional_plotno = strtoupper($_REQUEST['additional_plotno_road'.$i.'_'.$c]);
  //           if($additional_plotno!="" && $additional_plotno!=null){

  //             $stmt_plot = $obj->con1->prepare("INSERT INTO `pr_estate_roadplot`(`industrial_estate_id`, `road_no`, `plot_start_no`, `user_id`) VALUES (?,?,?,?)");
  //             $stmt_plot->bind_param("issi",$insert_id,$road_number,$additional_plotno,$user_id);
  //             $Resp=$stmt_plot->execute();
  //             $stmt_plot->close();

  //             $cp = Array (
  //                 "post_fields" => Array (
  //                 "source" => "",
  //                 "Source_Name" => "",
  //                 "Contact_Name" => "",
  //                 "Mobile_No" => "",
  //                 "Email" => "",
  //                 "Designation_In_Firm" => "",
  //                 "Firm_Name" => "",
  //                 "GST_No" => "",
  //                 "Type_of_Company" => "",
  //                 "Category" => "",
  //                 "Segment" => "",
  //                 "Premise" => "",
  //                 "Factory_Address" => "",
  //                 "state" => $state,
  //                 "city" => $city,
  //                 "Taluka" => $taluka,
  //                 "Area" => $area,
  //                 "IndustrialEstate" => $industrial_estate,
  //                 "loan_applied" => "",
  //                 "Completion_Date" => "",
  //                 "Term_Loan_Amount" => "",
  //                 "CC_Loan_Amount" => "",
  //                 "Under_Process_Bank" => "",
  //                 "Under_Process_Branch" => "",
  //                 "Term_Loan_Amount_In_Process" => "",
  //                 "Under_Process_Date" => "",
  //                 "ROI" => "",
  //                 "Colletral" => "",
  //                 "Consultant" => "",
  //                 "Sanctioned_Bank" => "",
  //                 "Bank_Branch" => "",
  //                 "DOS" => "",
  //                 "TL_Amount" => "",
  //                 "Sactioned_Loan_Consultant" => "",
  //                 "category_type" => "",
  //                 "Remarks" => ""
  //               ),
  //               "inq_submit" => "Submit",
  //               "bad_lead_reason" => "",
  //               "bad_lead_reason_remark" => "",
  //               "Image" => "",
  //               "Constitution" => "",
  //               "Status" => "",
  //               "plot_details" => Array(
  //                 Array(
  //                   "Plot_No" => $additional_plotno,
  //                   "Floor" => "0",
  //                   "Road_No" => $road_number,
  //                   "Plot_Status" => "",
  //                   "Plot_Id" => "1",
  //                 ),
  //               ) 
  //             );
               
  //             // Encode array to json
  //             $json = json_encode($cp);
               
  //             // Display it
  //             //echo "<br/>"."$json";

  //             $Resp = tbl_tdrawdata_insert($json,$user_id);
              
  //             company_plot_insert($additional_plotno,$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //           }
  //         }
  //       }
  //     }
  //   }

  //   // for additional road
  //   if($num_of_additional_roads>0){
  //     for($r=0;$r<$num_of_additional_roads;$r++){
        
  //       $road_number = strtoupper($_REQUEST['additional_road_no'.$r]);
  //       $road_plot_cnt = $_REQUEST['additional_road_plot_cnt'.$r];
  //       $from_to_plot_cnt = $_REQUEST['from_to_plot_cnt_foradditional'.$r];

  //       for($ft=0;$ft<$from_to_plot_cnt;$ft++){
  //         $from_plotno_road = strtoupper($_REQUEST['additional_from_plotno_road'.$r.'_'.$ft]);
  //         $to_plotno_road = strtoupper($_REQUEST['additional_to_plotno_road'.$r.'_'.$ft]);

  //         $letters = "/^[A-Za-z]$/";
  //         $suffix = "/^[0-9]+[^a-zA-Z0-9]*[a-zA-Z]$/";
  //         $prefix = "/^[a-zA-Z][^a-zA-Z0-9]*[0-9]+$/";
  //         $specialChars ="/[`!@#$%^&*()_\-+=\[\]{};':\\|,.<>\/?~ ]+/";
  //         $re_for_alphabet = "/([a-zA-Z]+)/";
  //         $re_for_digits = "/(\d+)/";

  //         if($from_plotno_road!="" && $from_plotno_road!=null){

  //           $stmt_plot = $obj->con1->prepare("INSERT INTO `pr_estate_roadplot`(`industrial_estate_id`, `road_no`, `plot_start_no`, `plot_end_no`, `user_id`) VALUES (?,?,?,?,?)");
  //           $stmt_plot->bind_param("isssi",$insert_id,$road_number,$from_plotno_road,$to_plotno_road,$user_id);
  //           $Resp=$stmt_plot->execute();
  //           $stmt_plot->close();

  //           if(is_numeric($from_plotno_road) && is_numeric($to_plotno_road)){
  //             $type = "numeric";
  //           }
  //           else if(preg_match($letters,$from_plotno_road) && preg_match($letters,$to_plotno_road)){
  //             $type = "alphabet";
  //           }
  //           else if(preg_match($prefix,$from_plotno_road) && preg_match($prefix,$to_plotno_road)){
  //             $type = "prefix";
  //           }
  //           else if(preg_match($suffix,$from_plotno_road) && preg_match($suffix,$to_plotno_road)){
  //             $type = "suffix";
  //           }


  //           if($type=="numeric"){
  //             for($p=$from_plotno_road;$p<=$to_plotno_road;$p++){
  //               $cp = Array (
  //                   "post_fields" => Array (
  //                   "source" => "",
  //                   "Source_Name" => "",
  //                   "Contact_Name" => "",
  //                   "Mobile_No" => "",
  //                   "Email" => "",
  //                   "Designation_In_Firm" => "",
  //                   "Firm_Name" => "",
  //                   "GST_No" => "",
  //                   "Type_of_Company" => "",
  //                   "Category" => "",
  //                   "Segment" => "",
  //                   "Premise" => "",
  //                   "Factory_Address" => "",
  //                   "state" => $state,
  //                   "city" => $city,
  //                   "Taluka" => $taluka,
  //                   "Area" => $area,
  //                   "IndustrialEstate" => $industrial_estate,
  //                   "loan_applied" => "",
  //                   "Completion_Date" => "",
  //                   "Term_Loan_Amount" => "",
  //                   "CC_Loan_Amount" => "",
  //                   "Under_Process_Bank" => "",
  //                   "Under_Process_Branch" => "",
  //                   "Term_Loan_Amount_In_Process" => "",
  //                   "Under_Process_Date" => "",
  //                   "ROI" => "",
  //                   "Colletral" => "",
  //                   "Consultant" => "",
  //                   "Sanctioned_Bank" => "",
  //                   "Bank_Branch" => "",
  //                   "DOS" => "",
  //                   "TL_Amount" => "",
  //                   "Sactioned_Loan_Consultant" => "",
  //                   "category_type" => "",
  //                   "Remarks" => ""
  //                 ),
  //                 "inq_submit" => "Submit",
  //                 "bad_lead_reason" => "",
  //                 "bad_lead_reason_remark" => "",
  //                 "Image" => "",
  //                 "Constitution" => "",
  //                 "Status" => "",
  //                 "plot_details" => Array(
  //                   Array(
  //                   "Plot_No" => $p,
  //                   "Floor" => "0",
  //                   "Road_No" => $road_number,
  //                   "Plot_Status" => "",
  //                   "Plot_Id" => "1",
  //                   ),
  //                 ) 
  //               );
                 
  //               // Encode array to json
  //               $json = json_encode($cp);
                 
  //               // Display it
  //               //echo "$json";

  //               $Resp = tbl_tdrawdata_insert($json,$user_id);
                
  //               company_plot_insert($p,$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //             }
  //           }
  //           else if($type=="alphabet") {
  //             $from_plot_upper = strtoupper($from_plotno_road);
  //             $to_plot_upper = strtoupper($to_plotno_road);
  //             $from_plot_ascii = ord($from_plot_upper);
  //             $to_plot_ascii = ord($to_plot_upper);
  //             for($p=$from_plot_ascii;$p<=$to_plot_ascii;$p++){
  //               $cp = Array (
  //                   "post_fields" => Array (
  //                   "source" => "",
  //                   "Source_Name" => "",
  //                   "Contact_Name" => "",
  //                   "Mobile_No" => "",
  //                   "Email" => "",
  //                   "Designation_In_Firm" => "",
  //                   "Firm_Name" => "",
  //                   "GST_No" => "",
  //                   "Type_of_Company" => "",
  //                   "Category" => "",
  //                   "Segment" => "",
  //                   "Premise" => "",
  //                   "Factory_Address" => "",
  //                   "state" => $state,
  //                   "city" => $city,
  //                   "Taluka" => $taluka,
  //                   "Area" => $area,
  //                   "IndustrialEstate" => $industrial_estate,
  //                   "loan_applied" => "",
  //                   "Completion_Date" => "",
  //                   "Term_Loan_Amount" => "",
  //                   "CC_Loan_Amount" => "",
  //                   "Under_Process_Bank" => "",
  //                   "Under_Process_Branch" => "",
  //                   "Term_Loan_Amount_In_Process" => "",
  //                   "Under_Process_Date" => "",
  //                   "ROI" => "",
  //                   "Colletral" => "",
  //                   "Consultant" => "",
  //                   "Sanctioned_Bank" => "",
  //                   "Bank_Branch" => "",
  //                   "DOS" => "",
  //                   "TL_Amount" => "",
  //                   "Sactioned_Loan_Consultant" => "",
  //                   "category_type" => "",
  //                   "Remarks" => ""
  //                 ),
  //                 "inq_submit" => "Submit",
  //                 "bad_lead_reason" => "",
  //                 "bad_lead_reason_remark" => "",
  //                 "Image" => "",
  //                 "Constitution" => "",
  //                 "Status" => "",
  //                 "plot_details" => Array(
  //                   Array(
  //                   "Plot_No" => chr($p),
  //                   "Floor" => "0",
  //                   "Road_No" => $road_number,
  //                   "Plot_Status" => "",
  //                   "Plot_Id" => "1",
  //                   ),
  //                 ) 
  //               );
                 
  //               // Encode array to json
  //               $json = json_encode($cp);
                 
  //               // Display it
  //               //echo "$json";

  //               $Resp = tbl_tdrawdata_insert($json,$user_id);
                
  //               company_plot_insert(chr($p),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //             }
  //           }
  //           else if($type=="prefix") {

  //             preg_match($re_for_digits,$from_plotno_road,$from_plot_number);
  //             preg_match($re_for_alphabet,$from_plotno_road,$from_plot_alphabet);
  //             preg_match($specialChars,$from_plotno_road,$from_plot_char);

  //             preg_match($re_for_digits,$to_plotno_road,$to_plot_number);
  //             preg_match($re_for_alphabet,$to_plotno_road,$to_plot_alphabet);
  //             preg_match($specialChars,$to_plotno_road,$to_plot_char);

  //             if($to_plot_number[0]>=$from_plot_number[0] && $to_plot_alphabet[0]==$from_plot_alphabet[0]){
  //               // number increment
  //               for($p=$from_plot_number[0];$p<=$to_plot_number[0];$p++){
  //                 $cp = Array (
  //                     "post_fields" => Array (
  //                     "source" => "",
  //                     "Source_Name" => "",
  //                     "Contact_Name" => "",
  //                     "Mobile_No" => "",
  //                     "Email" => "",
  //                     "Designation_In_Firm" => "",
  //                     "Firm_Name" => "",
  //                     "GST_No" => "",
  //                     "Type_of_Company" => "",
  //                     "Category" => "",
  //                     "Segment" => "",
  //                     "Premise" => "",
  //                     "Factory_Address" => "",
  //                     "state" => $state,
  //                     "city" => $city,
  //                     "Taluka" => $taluka,
  //                     "Area" => $area,
  //                     "IndustrialEstate" => $industrial_estate,
  //                     "loan_applied" => "",
  //                     "Completion_Date" => "",
  //                     "Term_Loan_Amount" => "",
  //                     "CC_Loan_Amount" => "",
  //                     "Under_Process_Bank" => "",
  //                     "Under_Process_Branch" => "",
  //                     "Term_Loan_Amount_In_Process" => "",
  //                     "Under_Process_Date" => "",
  //                     "ROI" => "",
  //                     "Colletral" => "",
  //                     "Consultant" => "",
  //                     "Sanctioned_Bank" => "",
  //                     "Bank_Branch" => "",
  //                     "DOS" => "",
  //                     "TL_Amount" => "",
  //                     "Sactioned_Loan_Consultant" => "",
  //                     "category_type" => "",
  //                     "Remarks" => ""
  //                   ),
  //                   "inq_submit" => "Submit",
  //                   "bad_lead_reason" => "",
  //                   "bad_lead_reason_remark" => "",
  //                   "Image" => "",
  //                   "Constitution" => "",
  //                   "Status" => "",
  //                   "plot_details" => Array(
  //                     Array(
  //                     "Plot_No" => strtoupper($from_plot_alphabet[0]).$from_plot_char[0].$p,
  //                     "Floor" => "0",
  //                     "Road_No" => $road_number,
  //                     "Plot_Status" => "",
  //                     "Plot_Id" => "1",
  //                     ),
  //                   ) 
  //                 );
                   
  //                 // Encode array to json
  //                 $json = json_encode($cp);
                   
  //                 // Display it
  //                 //echo "$json";

  //                 $Resp = tbl_tdrawdata_insert($json,$user_id);
                  
  //                 company_plot_insert((strtoupper($from_plot_alphabet[0]).$from_plot_char[0].$p),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //               }
  //             }
  //             else if($to_plot_number[0]==$from_plot_number[0] && strtoupper($to_plot_alphabet[0])>=strtoupper($from_plot_alphabet[0])){
  //               // alphabet increment
  //               for($p=ord(strtoupper($from_plot_alphabet[0]));$p<=ord(strtoupper($to_plot_alphabet[0]));$p++){
  //                 $cp = Array (
  //                     "post_fields" => Array (
  //                     "source" => "",
  //                     "Source_Name" => "",
  //                     "Contact_Name" => "",
  //                     "Mobile_No" => "",
  //                     "Email" => "",
  //                     "Designation_In_Firm" => "",
  //                     "Firm_Name" => "",
  //                     "GST_No" => "",
  //                     "Type_of_Company" => "",
  //                     "Category" => "",
  //                     "Segment" => "",
  //                     "Premise" => "",
  //                     "Factory_Address" => "",
  //                     "state" => $state,
  //                     "city" => $city,
  //                     "Taluka" => $taluka,
  //                     "Area" => $area,
  //                     "IndustrialEstate" => $industrial_estate,
  //                     "loan_applied" => "",
  //                     "Completion_Date" => "",
  //                     "Term_Loan_Amount" => "",
  //                     "CC_Loan_Amount" => "",
  //                     "Under_Process_Bank" => "",
  //                     "Under_Process_Branch" => "",
  //                     "Term_Loan_Amount_In_Process" => "",
  //                     "Under_Process_Date" => "",
  //                     "ROI" => "",
  //                     "Colletral" => "",
  //                     "Consultant" => "",
  //                     "Sanctioned_Bank" => "",
  //                     "Bank_Branch" => "",
  //                     "DOS" => "",
  //                     "TL_Amount" => "",
  //                     "Sactioned_Loan_Consultant" => "",
  //                     "category_type" => "",
  //                     "Remarks" => ""
  //                   ),
  //                   "inq_submit" => "Submit",
  //                   "bad_lead_reason" => "",
  //                   "bad_lead_reason_remark" => "",
  //                   "Image" => "",
  //                   "Constitution" => "",
  //                   "Status" => "",
  //                   "plot_details" => Array(
  //                     Array(
  //                     "Plot_No" => chr($p).$from_plot_char[0].$from_plot_number[0],
  //                     "Floor" => "0",
  //                     "Road_No" => $road_number,
  //                     "Plot_Status" => "",
  //                     "Plot_Id" => "1",
  //                     ),
  //                   ) 
  //                 );
                   
  //                 // Encode array to json
  //                 $json = json_encode($cp);
                   
  //                 // Display it
  //                 //echo "$json";

  //                 $Resp = tbl_tdrawdata_insert($json,$user_id);
                  
  //                 company_plot_insert((chr($p).$from_plot_char[0].$from_plot_number[0]),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //               }
  //             }
  //           }
  //           else if($type=="suffix") {
  //             preg_match($re_for_digits,$from_plotno_road,$from_plot_number);
  //             preg_match($re_for_alphabet,$from_plotno_road,$from_plot_alphabet);
  //             preg_match($specialChars,$from_plotno_road,$from_plot_char);

  //             preg_match($re_for_digits,$to_plotno_road,$to_plot_number);
  //             preg_match($re_for_alphabet,$to_plotno_road,$to_plot_alphabet);
  //             preg_match($specialChars,$to_plotno_road,$to_plot_char);

  //             if($to_plot_number[0]>=$from_plot_number[0] && $to_plot_alphabet[0]==$from_plot_alphabet[0]){
  //               // number increment
  //               for($p=$from_plot_number[0];$p<=$to_plot_number[0];$p++){
  //                 $cp = Array (
  //                     "post_fields" => Array (
  //                     "source" => "",
  //                     "Source_Name" => "",
  //                     "Contact_Name" => "",
  //                     "Mobile_No" => "",
  //                     "Email" => "",
  //                     "Designation_In_Firm" => "",
  //                     "Firm_Name" => "",
  //                     "GST_No" => "",
  //                     "Type_of_Company" => "",
  //                     "Category" => "",
  //                     "Segment" => "",
  //                     "Premise" => "",
  //                     "Factory_Address" => "",
  //                     "state" => $state,
  //                     "city" => $city,
  //                     "Taluka" => $taluka,
  //                     "Area" => $area,
  //                     "IndustrialEstate" => $industrial_estate,
  //                     "loan_applied" => "",
  //                     "Completion_Date" => "",
  //                     "Term_Loan_Amount" => "",
  //                     "CC_Loan_Amount" => "",
  //                     "Under_Process_Bank" => "",
  //                     "Under_Process_Branch" => "",
  //                     "Term_Loan_Amount_In_Process" => "",
  //                     "Under_Process_Date" => "",
  //                     "ROI" => "",
  //                     "Colletral" => "",
  //                     "Consultant" => "",
  //                     "Sanctioned_Bank" => "",
  //                     "Bank_Branch" => "",
  //                     "DOS" => "",
  //                     "TL_Amount" => "",
  //                     "Sactioned_Loan_Consultant" => "",
  //                     "category_type" => "",
  //                     "Remarks" => ""
  //                   ),
  //                   "inq_submit" => "Submit",
  //                   "bad_lead_reason" => "",
  //                   "bad_lead_reason_remark" => "",
  //                   "Image" => "",
  //                   "Constitution" => "",
  //                   "Status" => "",
  //                   "plot_details" => Array(
  //                     Array(
  //                     "Plot_No" => $p.$from_plot_char[0].strtoupper($from_plot_alphabet[0]),
  //                     "Floor" => "0",
  //                     "Road_No" => $road_number,
  //                     "Plot_Status" => "",
  //                     "Plot_Id" => "1",
  //                     ),
  //                   ) 
  //                 );
                   
  //                 // Encode array to json
  //                 $json = json_encode($cp);
                   
  //                 // Display it
  //                 //echo "$json";

  //                 $Resp = tbl_tdrawdata_insert($json,$user_id);
                  
  //                 company_plot_insert(($p.$from_plot_char[0]).strtoupper($from_plot_alphabet[0]),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //               }
  //             }
  //             else if($to_plot_number[0]==$from_plot_number[0] && strtoupper($to_plot_alphabet[0])>=strtoupper($from_plot_alphabet[0])){
  //               // alphabet increment
  //               for($p=ord(strtoupper($from_plot_alphabet[0]));$p<=ord(strtoupper($to_plot_alphabet[0]));$p++){
  //                 $cp = Array (
  //                     "post_fields" => Array (
  //                     "source" => "",
  //                     "Source_Name" => "",
  //                     "Contact_Name" => "",
  //                     "Mobile_No" => "",
  //                     "Email" => "",
  //                     "Designation_In_Firm" => "",
  //                     "Firm_Name" => "",
  //                     "GST_No" => "",
  //                     "Type_of_Company" => "",
  //                     "Category" => "",
  //                     "Segment" => "",
  //                     "Premise" => "",
  //                     "Factory_Address" => "",
  //                     "state" => $state,
  //                     "city" => $city,
  //                     "Taluka" => $taluka,
  //                     "Area" => $area,
  //                     "IndustrialEstate" => $industrial_estate,
  //                     "loan_applied" => "",
  //                     "Completion_Date" => "",
  //                     "Term_Loan_Amount" => "",
  //                     "CC_Loan_Amount" => "",
  //                     "Under_Process_Bank" => "",
  //                     "Under_Process_Branch" => "",
  //                     "Term_Loan_Amount_In_Process" => "",
  //                     "Under_Process_Date" => "",
  //                     "ROI" => "",
  //                     "Colletral" => "",
  //                     "Consultant" => "",
  //                     "Sanctioned_Bank" => "",
  //                     "Bank_Branch" => "",
  //                     "DOS" => "",
  //                     "TL_Amount" => "",
  //                     "Sactioned_Loan_Consultant" => "",
  //                     "category_type" => "",
  //                     "Remarks" => ""
  //                   ),
  //                   "inq_submit" => "Submit",
  //                   "bad_lead_reason" => "",
  //                   "bad_lead_reason_remark" => "",
  //                   "Image" => "",
  //                   "Constitution" => "",
  //                   "Status" => "",
  //                   "plot_details" => Array(
  //                     Array(
  //                     "Plot_No" => $from_plot_number[0].$from_plot_char[0].chr($p),
  //                     "Floor" => "0",
  //                     "Road_No" => $road_number,
  //                     "Plot_Status" => "",
  //                     "Plot_Id" => "1",
  //                     ),
  //                   ) 
  //                 );
                   
  //                 // Encode array to json
  //                 $json = json_encode($cp);
                   
  //                 // Display it
  //                 //echo "$json";

  //                 $Resp = tbl_tdrawdata_insert($json,$user_id);
                  
  //                 company_plot_insert(($from_plot_number[0].$from_plot_char[0].chr($p)),$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //               }
  //             }
  //           }
  //         }
  //       }

  //       if($from_plotno_road!="" && $from_plotno_road!=null){
  //         // for additional plot in road wise
  //         if($road_plot_cnt>0){
  //           for($c=0;$c<$road_plot_cnt;$c++){
  //             $additional_plotno = strtoupper($_REQUEST['additional_plotno_new_road'.$r.'_'.$c]);
  //             if($additional_plotno!="" && $additional_plotno!=null){

  //               $stmt_plot = $obj->con1->prepare("INSERT INTO `pr_estate_roadplot`(`industrial_estate_id`, `road_no`, `plot_start_no`, `user_id`) VALUES (?,?,?,?)");
  //               $stmt_plot->bind_param("issi",$insert_id,$road_number,$additional_plotno,$user_id);
  //               $Resp=$stmt_plot->execute();
  //               $stmt_plot->close();

  //               $cp = Array (
  //                   "post_fields" => Array (
  //                   "source" => "",
  //                   "Source_Name" => "",
  //                   "Contact_Name" => "",
  //                   "Mobile_No" => "",
  //                   "Email" => "",
  //                   "Designation_In_Firm" => "",
  //                   "Firm_Name" => "",
  //                   "GST_No" => "",
  //                   "Type_of_Company" => "",
  //                   "Category" => "",
  //                   "Segment" => "",
  //                   "Premise" => "",
  //                   "Factory_Address" => "",
  //                   "state" => $state,
  //                   "city" => $city,
  //                   "Taluka" => $taluka,
  //                   "Area" => $area,
  //                   "IndustrialEstate" => $industrial_estate,
  //                   "loan_applied" => "",
  //                   "Completion_Date" => "",
  //                   "Term_Loan_Amount" => "",
  //                   "CC_Loan_Amount" => "",
  //                   "Under_Process_Bank" => "",
  //                   "Under_Process_Branch" => "",
  //                   "Term_Loan_Amount_In_Process" => "",
  //                   "Under_Process_Date" => "",
  //                   "ROI" => "",
  //                   "Colletral" => "",
  //                   "Consultant" => "",
  //                   "Sanctioned_Bank" => "",
  //                   "Bank_Branch" => "",
  //                   "DOS" => "",
  //                   "TL_Amount" => "",
  //                   "Sactioned_Loan_Consultant" => "",
  //                   "category_type" => "",
  //                   "Remarks" => ""
  //                 ),
  //                 "inq_submit" => "Submit",
  //                 "bad_lead_reason" => "",
  //                 "bad_lead_reason_remark" => "",
  //                 "Image" => "",
  //                 "Constitution" => "",
  //                 "Status" => "",
  //                 "plot_details" => Array(
  //                   Array(
  //                     "Plot_No" => $additional_plotno,
  //                     "Floor" => "0",
  //                     "Road_No" => $road_number,
  //                     "Plot_Status" => "",
  //                     "Plot_Id" => "1",
  //                   ),
  //                 ) 
  //               );
                 
  //               // Encode array to json
  //               $json = json_encode($cp);
                 
  //               // Display it
  //               //echo "<br/>"."$json";

  //               $Resp = tbl_tdrawdata_insert($json,$user_id);
                
  //               company_plot_insert($additional_plotno,$floor_no,$road_number,$plot_id,$insert_id,$user_id);
  //             }
  //           }
  //         }
  //       }
  //     }
  //   }
  // }

  
  if(!$Resp)
  {
    throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
  }
} 
catch(\Exception  $e) {
  setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
}

if($Resp)
{
  setcookie("msg", "data",time()+3600,"/");
 // header("location:add_industrial_estate.php");
}
else
{
  setcookie("msg", "fail",time()+3600,"/");
  //header("location:add_industrial_estate.php");
}

?>