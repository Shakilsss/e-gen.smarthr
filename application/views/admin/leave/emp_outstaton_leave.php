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
                    <h3 class="box-title">Out Station Leave</h3>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> From Date <span style="color:red">*</span></label>
                                        <input required class="form-control" type="date" name="from_date" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> To Date <span style="color:red">*</span></label>
                                        <input required class="form-control" type="date" name="to_date" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Status <span style="color:red">*</span></label>
                                        <select required name="status" class="form-control">
                                            <option value="">select one</option>
                                            <option value="1">Draft</option>
                                            <option value="2">Forward to Approver</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
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
                            <th>from date </th>
                            <th>to date</th>
                            <th>app. From date</th>
                            <th>app. To date</th>
                            <th>app. days</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach($results as $k => $res) { ?>
                        <tr>
                            <td><?php echo $k+1; ?></td>
                            <td><?php echo date("d-m-Y", strtotime($res->from_date)) ;?></td>
                            <td><?php echo date("d-m-Y", strtotime($res->to_date)) ;?></td>
                            <?php if ($res->status == 3) { ?>
                            <td><?php echo date("d-m-Y", strtotime($res->ap_from_date)) ;?></td>
                            <td><?php echo date("d-m-Y", strtotime($res->ap_to_date)) ;?></td>
                            <td><?php echo $res->ap_day;?></td>
                            <?php } else { ?>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <?php } ?>
                            <?php if ($res->status == 1) {
                                $status = 'Draft';
                            } elseif ($res->status == 2) {
                                $status = 'On process';
                            } elseif ($res->status == 3) {
                                $status = 'Head Approved';
                            } else if($res->status == 6) {
                                $status = 'Approved';
                            } else {
                                $status = 'Rejected';
                            } ?>

                            <td><?= $status; ?></td>
                            <td><?php echo $res->remark;?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="<?php echo base_url();?>admin/leave/emp_outstaton_edit/<?php echo $res->id;?>"><i class="fa fa-pencil-square-o"></i> Edit</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </thead>
                </table>
            </div>
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
