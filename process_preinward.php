<?php
include "header.php";
error_reporting(E_ALL);
// $service_id = $_COOKIE['service_id'];
// $user_id = 29;
// // $stmt_stage = $obj->con1->prepare("SELECT DISTINCT(s1.stage_name), a1.service_id, a1.stage_id from (select MAX(t2.tatassign_id) as assign_id from tbl_tdtatassign t1, tbl_tdtatassign t2 where t1.tatassign_id=t2.tatassign_id GROUP BY t2.tatassign_inq_id) as tbl1, tbl_tdtatassign a1, tbl_tdstages s1 where tbl1.assign_id=a1.tatassign_id and a1.stage_id=s1.stage_id and a1.tatassign_user_id=? and a1.service_id=? and a1.stage_id in (select DISTINCT(stage_id) from pr_file_format where scheme_id=?)");
// // $stmt_stage->bind_param("iii", $user_id, $service_id, $service_id);
// // $stmt_stage->execute();
// // $stage_result = $stmt_stage->get_result();
// // $stmt_stage->close();


// $stmt_stage = $obj->con1->prepare("
//     SELECT 
//         s1.stage_name, 
//         a1.service_id, 
//         a1.stage_id
//     FROM (
//         SELECT MAX(t2.tatassign_id) as assign_id
//         FROM tbl_tdtatassign t1, tbl_tdtatassign t2
//         WHERE t1.tatassign_id = t2.tatassign_id
//         GROUP BY t2.tatassign_inq_id
//     ) as tbl1
//     JOIN tbl_tdtatassign a1 ON tbl1.assign_id = a1.tatassign_id
//     JOIN tbl_tdstages s1 ON a1.stage_id = s1.stage_id
//     WHERE a1.tatassign_user_id = ?
//       AND a1.service_id = ?
//       AND a1.stage_id IN (
//           SELECT DISTINCT(stage_id) FROM pr_file_format WHERE scheme_id = ?
//       )
//     GROUP BY a1.stage_id, a1.service_id
// ");
// $stmt_stage->bind_param("iii", $user_id, $service_id, $service_id);
// $stmt_stage->execute();
// $stage_result = $stmt_stage->get_result();
// $stmt_stage->close();
// $total_count = mysqli_num_rows($stage_result);

//added by arya - 31/01/25
if (isset($_REQUEST['btn_agreement_format'])) {
    $scheme_id = $_REQUEST['scheme_id'];
    $stage_id = $_REQUEST['stage_id'];
    $file_id = $_REQUEST['file_id'];
    $inq_id = $_REQUEST['inq_id'];

    $scheme_name = $_REQUEST['scheme_name'];
    $contact_person = $_REQUEST['contact_person'];
    $designation = $_REQUEST['designation'];
    $contact_no = $_REQUEST['contact_no'];
    $receivable_amount = $_REQUEST['receivable_amount'];
    $electricity_duty_exemption = $_REQUEST['electricity_duty_exemption'];
    $at_time_of_sanction = $_REQUEST['at_time_of_sanction'];
    $at_time_of_visit = $_REQUEST['at_time_of_visit'];
    $at_time_of_refund = $_REQUEST['at_time_of_refund'];
    $company_type = $_REQUEST['company_type'];
    $marketing_executive_name = $_REQUEST['marketing_executive_name'];
    $status = 'Completed';
    try {
        $cp = array(
            "scheme_name" => $scheme_name,
            "contact_person" => $contact_person,
            "designation" => $designation,
            "contact_no" => $contact_no,
            "receivable_amount" => $receivable_amount,
            "electricity_duty_exemption" => $electricity_duty_exemption,
            "at_time_of_sanction" => $at_time_of_sanction,
            "at_time_of_visit" => $at_time_of_visit,
            "at_time_of_refund" => $at_time_of_refund,
            "company_type" => $company_type,
            "marketing_executive_name" => $marketing_executive_name,
            "status" => $status
        );

        // Encode array to json
        $json = json_encode($cp);

        $stmt = $obj->con1->prepare("INSERT INTO `pr_files_data`(`scheme_id`, `stage_id`, `file_id`, `inq_id`, `file_data`, `status`) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("iiiiss", $scheme_id, $stage_id, $file_id, $inq_id, $json, $status);
        $Resp = $stmt->execute();

        if (!$Resp) {
            throw new Exception("Problem in inserting! " . strtok($obj->con1->error, '('));
        }
        $stmt->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data", time() + 3600, "/");
        header("location:process_preinward.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:proc??ess_preinward.php");
    }
}
//added by arya - 31/01/25

if (isset($_REQUEST['btn_update_agreement_format'])) {

    $pr_file_data_id = $_REQUEST['pr_file_data_id'];
    $scheme_id = $_REQUEST['scheme_id'];
    $stage_id = $_REQUEST['stage_id'];
    $file_id = $_REQUEST['file_id'];
    $inq_id = $_REQUEST['inq_id'];

    $scheme_name = $_REQUEST['scheme_name'];
    $contact_person = $_REQUEST['contact_person'];
    $designation = $_REQUEST['designation'];
    $contact_no = $_REQUEST['contact_no'];
    $receivable_amount = $_REQUEST['receivable_amount'];
    $electricity_duty_exemption = $_REQUEST['electricity_duty_exemption'];
    $at_time_of_sanction = $_REQUEST['at_time_of_sanction'];
    $at_time_of_visit = $_REQUEST['at_time_of_visit'];
    $at_time_of_refund = $_REQUEST['at_time_of_refund'];
    $marketing_executive_name = $_REQUEST['marketing_executive_name'];
    $status = 'Completed';

    try {
        // echo "UPDATE pr_files_data SET file_data = JSON_SET(file_data, '$.scheme_name', '".$scheme_name."', '$.contact_person' , '".$contact_person."', '$.designation' , '".$designation."', '$.contact_no' , '".$contact_no."', '$.receivable_amount' , '".$receivable_amount."', '$.electricity_duty_exemption' , '".$electricity_duty_exemption."', '$.at_time_of_sanction' , '".$at_time_of_sanction."', '$.at_time_of_visit' , '".$at_time_of_visit."', '$.at_time_of_refund' , '".$at_time_of_refund."', '$.company_type' , '".$company_type."', '$.marketing_executive_name' , '".$marketing_executive_name."', '$.status' , '".$status."' ) WHERE id='".$pr_file_data_id."'";
        $stmt = $obj->con1->prepare("UPDATE pr_files_data SET file_data = JSON_SET(file_data, '$.scheme_name', ?, '$.contact_person' , ?, '$.designation' , ?, '$.contact_no' , ?, '$.receivable_amount' , ?, '$.electricity_duty_exemption' , ?, '$.at_time_of_sanction' , ?, '$.at_time_of_visit' , ?, '$.at_time_of_refund' , ?, '$.company_type' , ?, '$.marketing_executive_name' , ?, '$.status' , ? ) WHERE id=?");
        $stmt->bind_param("ssssssssssssi", $scheme_name, $contact_person, $designation, $contact_no, $receivable_amount, $electricity_duty_exemption, $at_time_of_sanction, $at_time_of_visit, $at_time_of_refund, $company_type, $marketing_executive_name, $status, $pr_file_data_id);
        $Resp = $stmt->execute();

        if (!$Resp) {
            throw new Exception("Problem in updating! " . strtok($obj->con1->error, '('));
        }
        $stmt->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:process_preinward.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:process_preinward.php");
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
?>




<!-- accordian test -->
<div class="col-md mb-4 mb-md-0">
    <!-- <small class="text-light fw-semibold">Basic Accordion</small> -->
    <div class="accordion mt-3" id="accordionExample">
        <?php
        $j = 0;
        // while ($stage = mysqli_fetch_array($stage_result)) {
        ?>
        <div class="card accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                    data-bs-target="#accordion<?php echo $j ?>" aria-expanded="false"
                    aria-controls="accordion<?php echo $j ?>"><?php echo 'pre-inward' ?></button>
            </h2>

            <div id="accordion<?php echo $j ?>" class="accordion-collapse collapse show"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">


                    <!-- nested accordion -->
                    <div class="col-md mb-4 mb-md-0">
                        <div class="accordion mt-3" id="accordionCompany">

                            <?php
                            // $stmt_list = $obj->con1->prepare("SELECT a1.stage_id, a1.tatassign_inq_id, a1.tatassign_user_id, r1.raw_data from (SELECT MAX(t2.tatassign_id) as assign_id from tbl_tdtatassign t1, tbl_tdtatassign t2 where t1.tatassign_id=t2.tatassign_id GROUP BY t2.tatassign_inq_id) as tbl1, tbl_tdtatassign a1, tbl_tdrawdata r1 where tbl1.assign_id=a1.tatassign_id and a1.tatassign_inq_id=r1.id and a1.tatassign_user_id=? and a1.stage_id=? and a1.service_id=?");x
                            $stmt_list = $obj->con1->prepare("select tbl_tdrawdata.raw_data, tbl_tdapplication.ts, tbl_tdapplication.inq_id as tatassign_inq_id, tbl_tdapplication.id, tbl_tdapplication.app_data, tbl_tdapplication.id as application_id, tbl_tdrawassign.stage, 0 as stage_id, tbl_users.name, sq.user_id as last_user_id, sq.stage as last_stage from tbl_tdrawassign inner join tbl_users on tbl_users.id = tbl_tdrawassign.user_id inner join tbl_tdrawdata on tbl_tdrawdata.id = tbl_tdrawassign.inq_id inner join ( SELECT * FROM tbl_tdrawassign where id IN ( SELECT max(id) FROM `tbl_tdrawassign` group by inq_id ) ) sq on sq.inq_id = tbl_tdrawassign.inq_id INNER JOIN tbl_tdapplication on sq.inq_id = tbl_tdapplication.inq_id where tbl_tdrawassign.id IN ( SELECT max(id) as max_id FROM `tbl_tdrawassign` WHERE user_id='29' group by inq_id ) and sq.stage='preinward' and sq.user_id='29' group by tbl_tdapplication.inq_id LIMIT 100;");
                            // $stmt_list->bind_param("iii", $user_id, $stage['stage_id'], '0');
                            $stmt_list->execute();
                            $result = $stmt_list->get_result();
                            $stmt_list->close();
                            $i = 1;

                            while ($data = mysqli_fetch_array($result)) {
                                $row_data = json_decode($data["raw_data"]);
                                $post_fields = $row_data->post_fields;
                                ?>

                                <div class="card shadow-none bg-transparent border border-info mb-3 accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                                            data-bs-target="#compAccordion<?php echo $i ?>" aria-expanded="false"
                                            aria-controls="compAccordion<?php echo $i ?>">Company Name :
                                            <?php echo $post_fields->Firm_Name ?></button>
                                    </h2>

                                    <div id="compAccordion<?php echo $i ?>" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionCompany">
                                        <div class="accordion-body">

                                            <div class="card">
                                                <!-- <h5 class="card-header">Records</h5> -->
                                                <div class="table-responsive text-nowrap">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>File Name</th>
                                                                <th>Type</th>
                                                                <th></th>
                                                                <th><a href="javascript:download_zip('<?php echo $data['tatassign_inq_id'] ?>',0,0,'pre-inward')"
                                                                        class="btn btn-primary"
                                                                        style="margin-right:15px; color: #fff;">Download
                                                                        Zip</a></th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0">

                                                            <?php
                                                            $stmt_file = $obj->con1->prepare("SELECT * FROM `pr_file_format` WHERE scheme_id=0 and stage_id=0");
                                                            $stmt_file->execute();
                                                            $file_result = $stmt_file->get_result();
                                                            $stmt_file->close();

                                                            while ($files_res = mysqli_fetch_array($file_result)) {
                                                                $stmt_file_status = $obj->con1->prepare("SELECT status FROM `pr_files_data` WHERE scheme_id=0 and stage_id=0 and file_id=? and inq_id=?");
                                                                $stmt_file_status->bind_param("ii", $files_res['fid'], $data['tatassign_inq_id']);
                                                                $stmt_file_status->execute();
                                                                $file_status_result = $stmt_file_status->get_result();
                                                                $stmt_file_status->close();

                                                                $count = mysqli_num_rows($file_status_result);
                                                                $status_res = $file_status_result->fetch_assoc();
                                                                ?>



                                                                <tr>
                                                                    <td id="<?php echo $files_res['page_name'] ?>" hidden>
                                                                        <?php echo $files_res['fid'] ?>
                                                                    </td>
                                                                    <td><?php echo $files_res['file_name'] ?></td>
                                                                    <td><?php echo $files_res['doc_type'] ?></td>
                                                                    <td>
                                                                        <?php if ($files_res['get_data_type'] == "retrieve" || $files_res['get_data_type'] == "calculate") { ?>
                                                                            <a
                                                                                href="javascript:file_set_values('<?php echo $files_res['page_name'] ?>','<?php echo $data['tatassign_inq_id'] ?>','<?php echo '0' ?>',0,'<?php echo $files_res['fid'] ?>','<?php echo $count ?>','<?php echo $files_res['get_data_type'] ?>')">Fill
                                                                                Data</a>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td><?php if ($count > 0 || $files_res['get_data_type'] == "fetch") { ?>
                                                                            <a
                                                                                href="javascript:download_file('<?php echo $files_res['doc_file'] ?>','<?php echo $files_res['page_name'] ?>','<?php echo $data['tatassign_inq_id'] ?>',0,0,'<?php echo $files_res['fid'] ?>','<?php echo $count ?>','<?php echo $files_res['get_data_type'] ?>','<?php echo $files_res['doc_type'] ?>')">Download</a>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td><?php
                                                                    if ($files_res['get_data_type'] == "fetch") {
                                                                        echo "Completed";
                                                                    } else {
                                                                        if ($count > 0) {
                                                                            echo $status_res['status'];
                                                                        } else {
                                                                            echo "Pending";
                                                                        }
                                                                    }
                                                                    ?></td>
                                                                </tr>

                                                            <?php } ?>

                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <?php
                                $i++;
                            }
                            ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php
        $j++;
        // }
        ?>

    </div>
</div>

<!-- accorduian test end -->




<!-- Modal -->
<div class="modal fade" id="modalCenter" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Fill Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div id="modal_form_div"></div>
            </form>
        </div>
    </div>
</div>
<!-- /modal-->


<script type="text/javascript">

    function file_set_values(page_name, inq_id, service_id, stage_id, file_id, count, get_data_type) {
        if (get_data_type == "retrieve") {
            $('#modalCenter').modal('toggle');
            $.ajax({
                async: true,
                type: "POST",
                //url: "file_modals.php?action="+page_name,
                url: "file_modals.php?action=" + 'agreement_format',
                data: "scheme_id=" + service_id + "&stage_id=" + stage_id + "&file_id=" + file_id + "&inq_id=" + inq_id,
                cache: false,
                success: function (result) {
                    $('#modal_form_div').html('');
                    $('#modal_form_div').html(result);
                }
            });
        }
        else if (get_data_type == "calculate") {
            if (page_name == "employment_data_gogtp") {
                affidavit_id = $('#affidavit2_gogtp').text();
                $('#modalCenter').modal('toggle');
                $.ajax({
                    async: true,
                    type: "POST",
                    url: "file_modals.php?action=" + page_name,
                    data: "scheme_id=" + service_id + "&stage_id=" + stage_id + "&file_id=" + file_id + "&inq_id=" + inq_id + "&affidavit_id=" + affidavit_id,
                    cache: false,
                    success: function (result) {
                        $('#modal_form_div').html('');
                        $('#modal_form_div').html(result);
                    }
                });
            }
        }
        else if (get_data_type == "fetch") { }
    }

    // function download_file(page_name,inq_id,service_id,stage_id,file_id,count,get_data_type) {
    //  window.location = page_name+".php?inq_id="+inq_id+"&service_id="+service_id+"&stage_id="+stage_id+"&file_id="+file_id;
    // }
    function download_file(doc_file, page_name, inq_id, service_id, stage_id, file_id, count, get_data_type, document_type) {
        window.location = "download_single_file.php?inq_id=" + inq_id + "&service_id=" + service_id + "&stage_id=" + stage_id + "&file_id=" + file_id + "&doc_file=" + doc_file + "&doc_type=" + document_type;
    }


    function download_zip(inq_id, service_id, stage_id, stage_name) {
        window.location = "zip_download_sanction_office.php?inq_id=" + inq_id + "&service_id=" + service_id + "&stage_id=" + stage_id;
    }
</script>
<?php
include "footer.php";
?>