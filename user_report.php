<?php
include("header.php");

$user_id = $_SESSION["id"];

// update data
if (isset($_REQUEST['btn_modal_update'])) {
    $industrial_estate_id = $_REQUEST['industrial_estate_id'];
    $verify_status = $_REQUEST['verify_status'];

    try {
        $stmt = $obj->con1->prepare("UPDATE `pr_add_industrialestate_details` SET `status`=? WHERE `industrial_estate_id`=?");
        $stmt->bind_param("si", $verify_status, $industrial_estate_id);
        $Resp = $stmt->execute();

        if (!$Resp) {
            throw new Exception("Problem in adding! " . strtok($obj->con1->error, '('));
        }
        $stmt->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data", time() + 3600, "/");
        header("location:estate_status_report.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:estate_status_report.php");
    }
}

?>

<h4 class="fw-bold py-3 mb-4">User Report</h4>

<?php
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

<?php if (in_array($user_id, $admin)) { ?>
    <!-- grid -->

    <!-- Basic Bootstrap Table -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">

            <form method="post" class="d-flex gap-3 align-items-center" hx-post="user_report_filter_table.php"
                hx-target="#table_body,#table_caption" hx-swap="multi:#table_caption:outeHTML,#table_body:outerHTML">
                <div class="input-group">
                    <label class="input-group-text">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
                <div class="input-group">
                    <label class="input-group-text">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
                <div class="input-group">
                    <!-- <label class="input-group-text">User</label> -->
                    <select name="select_user_id" id="select_user_id" class="form-select">
                        <option value="">Select User</option>
                        <?php

                        // tbl_user - user
                        // tbl_industrial_estate
                        // pr_user_Activity 
                        // tbl_/pr_company
                    
                        // $stmt_list = $obj->con1->prepare("SELECT cid, industrial_estate, area, taluka, company_id, sum(count) as total_count FROM pr_visit_count group by company_id");
                        $stmt_list = $obj->con1->prepare("SELECT DISTINCT(a.name), a.id from tbl_users as a RIGHT join pr_user_activity as b on a.id = b.user_id;");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        $stmt_list->close();

                        while ($data = mysqli_fetch_array($result)) { ?>
                            <option value="<?= $data["id"] ?>"><?= $data["name"] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <input type="button" class="btn btn-primary" name="btn_excel" value="Download Excel"
                onClick="javascript:plottingGrid('<?php echo isset($_REQUEST['industrial_estate']) ? $_REQUEST['industrial_estate'] : "" ?>',
                                               '<?php echo isset($_REQUEST['company']) ? $_REQUEST['company'] : "" ?>',
                                               '<?php echo isset($_COOKIE['name']) ? $_COOKIE['name'] : "" ?>',
                                               '<?php echo isset($_REQUEST['operation']) ? $_REQUEST['operation'] : "" ?>',
                                               '<?php echo isset($_REQUEST['date_time']) ? $_REQUEST['date_time'] : "" ?>')" id="btn_excel">
        </div>
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="table_id">
                <thead>
                    <tr>
                        <th>Srno</th>
                        <th>industrial estate</th>
                        <th>company</th>
                        <th>username</th>
                        <th>operation</th>
                        <th>date time</th>
                    </tr>
                </thead>
                <caption class="caption-top fw-bold fst-italic text-dark" id="table_caption"></caption>
                <tbody class="table-border-bottom-0" id="table_body">
                    <?php

                    // tbl_user - user
                    // tbl_industrial_estate
                    // pr_user_Activity 
                    // tbl_/pr_company
                
                    // $stmt_list = $obj->con1->prepare("SELECT cid, industrial_estate, area, taluka, company_id, sum(count) as total_count FROM pr_visit_count group by company_id");
                    $stmt_list = $obj->con1->prepare("SELECT * FROM pr_user_activity p1, tbl_users u1 where user_id = u1.id  group by p1.industrial_estate,p1.company,p1.user_id,p1.operation,p1.date_time order by p1.id desc;");
                    $stmt_list->execute();
                    $result = $stmt_list->get_result();
                    $stmt_list->close();
                    $i = 1;

                    while ($data = mysqli_fetch_array($result)) {
                        ?>

                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $data["industrial_estate"] ?></td>
                            <td><?php echo ($data["company"]!="")?$data["company"]:"-" ?></td>
                            <td><?php echo $data["name"] ?></td>
                            <td><?php echo $data["operation"] ?></td>
                            <td><?php echo date("d-m-Y h:i A", strtotime($data["date_time"])) ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

    <!-- / grid -->

<?php } ?>

<!-- Modal -->
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div>
                <form method="post">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" class="form-control" name="industrial_estate_id"
                                id="industrial_estate_id" />

                            <div class="mb-3">
                                <label class="form-label" for="basic-default-fullname"
                                    id="industrial_estate"></label><br />
                                <label class="form-label" for="basic-default-fullname" id="taluka"></label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="basic-default-fullname">Status</label><br />
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="verify_status" id="Verified"
                                        value="Verified" required>
                                    <label class="form-check-label" for="inlineRadio1">Verified</label>
                                </div>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="verify_status" id="Fake"
                                        value="Fake" required>
                                    <label class="form-check-label" for="inlineRadio1">Fake</label>
                                </div>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="verify_status" id="Duplicate"
                                        value="Duplicate" required>
                                    <label class="form-check-label" for="inlineRadio1">Duplicate</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" name="btn_modal_update" id="btn_modal_update"
                            value="Save Changes">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- /modal-->

<!-- / Content -->
<script type="text/javascript">

    function editdata(id, state, city, taluka, area, industrial_estate, status) {
        $('#modalCenter').modal('toggle');
        $('#industrial_estate_id').val(id);
        $('#industrial_estate').html("Industrial Estate : " + industrial_estate);
        $('#taluka').html("Taluka : " + taluka);
        $('#' + status).attr("checked", "checked");
    }

    function plottingGrid() {
        const start_date = $('#start_date').val();
        const end_date = $('#end_date').val();
        const select_user_id = $('#select_user_id').val();

        if (start_date || end_date || select_user_id) {
            window.open(`user_report_filter_excel.php?start_date=${start_date}&end_date=${end_date}&select_user_id=${select_user_id}`, '_blank');
            return;
        }
        window.open('user_report_excel.php', '_blank');
        // document.cookie = "report_search=" + arr;
    }

</script>

<?php
include("footer.php");
?>