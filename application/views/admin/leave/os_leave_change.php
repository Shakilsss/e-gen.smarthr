<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $session = $this->session->userdata('username');?>

<div class="row">
    <div class="col-md-12">
        <?php if($this->session->flashdata('success')):?>
            <div class="alert alert-success">
                <?=$this->session->flashdata('success');;?>
            </div>
        <?php endif; ?>

        <div class="box mb-4 <?php echo $get_animate;?>">
            <div class="box-header with-border">
                <h3 class="box-title">Out Station Leave</h3>
            </div>
        </div>

        <div class="box mb-4 <?php echo $get_animate;?>">
            <br>
            <div class="form-body">
                <!-- employee information -->
                <div class="col-md-4">
                    <div class="col-md-12">
                        <p>Employee Information </p>
                    </div>
                    <br><br>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Name : </label><br>
                            <span><?= $info->first_name .' '. $info->last_name ?></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Department : </label><br>
                            <span><?= $info->department_name ?></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Designation : </label><br>
                            <span><?= $info->designation_name ?></span>
                        </div>
                    </div>
                </div>

                <!-- leave apply information -->
                <div class="col-md-4">
                    <div class="col-md-12">
                        <p>Leave Apply Section </p>
                    </div>
                    <br><br>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label> From Date : </label><br>
                            <span><?= date('d-m-Y', strtotime($row->from_date)) ?></span>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label> To Date : </label><br>
                            <span><?= date('d-m-Y', strtotime($row->to_date)) ?></span>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label> Remark : </label><br>
                            <span><?= $info->remark ?></span>
                        </div>
                    </div>
                </div>

                <!-- leave approve section -->
                <?php $hidden = array('_user' => $session['user_id']);?>
                <?php echo form_open_multipart(current_url(), $hidden);?>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <p>Leave Approve Section</p>
                        </div>
                        <br><br>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label> From Date <span style="color:red">*</span></label>
                                <input required class="form-control" value="<?= $row->from_date ?>" type="date" name="ap_from_date" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> To Date <span style="color:red">*</span></label>
                                <input required class="form-control" value="<?= $row->to_date ?>" type="date" name="ap_to_date" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> Status <span style="color:red">*</span></label>
                                <select name="status" class="form-control">
                                    <option value="">Select one</option>
                                    <option value="3">Approve</option>
                                    <option value="4">Reject</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="padding-right: 30px;">
                        <button name="hrsale_form" type="submit" class="btn btn-primary"><i class="fa fa fa-check-square-o"></i> Save</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <br><br>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // get designations
        $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
        $('[data-plugin="select_hrm"]').select2({
            width: '100%'
        });
    });
</script>
