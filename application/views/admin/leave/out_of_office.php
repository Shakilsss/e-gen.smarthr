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
            <div id="accordion">
                <div class="box-header with-border">
                    <h3 class="box-title">Out Off Office</h3>
                    <div class="box-tools pull-right">
                        <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
                            <button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span>
                                <?php echo $this->lang->line('xin_add_new');?>
                            </button>
                        </a>
                    </div>
                </div>

                <div id="add_form" class="collapse add-form <?php echo $get_animate;?>" data-parent="#accordion">
                    <div class="box-body">
                        <?php $attributes = array('autocomplete' => 'off');?>
                        <?php $hidden = array('_user' => $session['user_id']);?>
                        <?php echo form_open_multipart(current_url(), $attributes, $hidden);?>

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label> Date <span style="color:red">*</span></label>
                                        <input required class="form-control" type="date" name="date" >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Office In Time <span style="color:red">*</span></label>
                                        <input class="form-control timepicker" name="in_time" placeholder="HH:MM">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Office Out Time <span style="color:red">*</span></label>
                                        <input class="form-control timepicker" name="out_time" placeholder="HH:MM">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Remark <span style="color:red">*</span></label>
                                        <textarea required class="form-control textarea" name="remark"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button name="hrsale_form" type="submit" class="btn btn-primary"><i class="fa fa fa-check-square-o"></i> Save</button>
                            </div>
                        </div>
                        <br>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="box mb-4 <?php echo $get_animate;?>">
            <div class="box-header with-border">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>date </th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Status</th>
                            <th>Remark</th>
                        </tr>

                        <?php foreach($results as $k => $res) { ?>
                        <tr>
                            <td><?php echo $k+1; ?></td>
                            <td><?php echo date("d-m-Y", strtotime($res->date)) ;?></td>
                            <td><?php echo $res->in_time == '00:00:00' ? '-' : $res->in_time ;?></td>
                            <td><?php echo $res->out_time == '00:00:00' ? '-' : $res->out_time ;?></td>
                            <?php if ($res->status == 1) {
                                $status = 'On process';
                            } elseif ($res->status == 2) {
                                $status = 'Approved';
                            } elseif ($res->status == 3) {
                                $status = 'Rejected';
                            } else {
                                $status = 'Delete';
                            } ?>

                            <td><?= $status; ?></td>
                            <td><?php echo $res->remark;?></td>
                        </tr>
                        <?php } ?>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.clockpicker').clockpicker();
            var input = $('.timepicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true,
            'default': 'now'
        });
    });

    $(document).ready(function() {
        // get designations
        $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
        $('[data-plugin="select_hrm"]').select2({
            width: '100%'
        });
    });
</script>
